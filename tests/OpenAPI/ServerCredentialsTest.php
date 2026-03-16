<?php

declare(strict_types=1);

namespace Tests\OpenAPI;

/**
 * Tests OpenAPI compliance for Server Credentials endpoints.
 *
 * This test class dynamically validates that all Server Credentials-tagged endpoints
 * in the OpenAPI spec have corresponding SDK methods and tests.
 */
class ServerCredentialsTest extends OpenAPITestCase
{
    protected static string $category = 'Server Credentials';

    public function test_all_server_credentials_endpoints_have_sdk_methods()
    {
        $endpoints = static::getEndpointsForTag(static::$category);
        $mapping = OpenAPIEndpointMapper::createMapping($endpoints);
        $uncovered = [];

        foreach ($endpoints as $endpoint) {
            $method = $endpoint['method'];
            $path = $endpoint['path'];
            $signature = "$method $path";

            $sdkMethod = $mapping[$signature] ?? null;

            if (! $sdkMethod) {
                $uncovered[] = "$signature (no SDK method mapped)";
                continue;
            }

            if (! isset(static::$sdkMethods[$sdkMethod])) {
                $uncovered[] = "$signature -> $sdkMethod() (SDK method not found)";
            }
        }

        if (! empty($uncovered)) {
            $this->fail(
                "The following ".count($uncovered)." Server Credentials endpoints do not have SDK methods:\n".
                implode("\n", $uncovered)
            );
        }

        $this->assertTrue(true);
    }

    public function test_all_server_credentials_sdk_methods_have_tests()
    {
        $endpoints = static::getEndpointsForTag(static::$category);
        $mapping = OpenAPIEndpointMapper::createMapping($endpoints);
        $expectedMethods = array_unique(array_values($mapping));
        $testedMethods = static::getTestedMethods();
        $internalMethods = static::getInternalMethods();
        $missingTests = [];

        foreach ($expectedMethods as $methodName) {
            // Skip if it's an internal method
            if (in_array($methodName, $internalMethods)) {
                continue;
            }

            // Check if SDK method exists
            if (! isset(static::$sdkMethods[$methodName])) {
                continue;
            }

            // Check if it has a test
            if (! isset($testedMethods[$methodName])) {
                $methodInfo = static::$sdkMethods[$methodName];
                $missingTests[] = $methodName.' ('.$methodInfo['class'].')';
            }
        }

        if (! empty($missingTests)) {
            $this->fail(
                "The following ".count($missingTests)." Server Credentials SDK methods do not have tests:\n".
                implode("\n", $missingTests)
            );
        }

        $this->assertTrue(true);
    }

    public function test_server_credentials_endpoint_coverage_statistics()
    {
        $endpoints = static::getEndpointsForTag(static::$category);
        $mapping = OpenAPIEndpointMapper::createMapping($endpoints);
        $testedMethods = static::getTestedMethods();

        $totalEndpoints = count($endpoints);
        $mappedEndpoints = 0;
        $testedEndpoints = 0;

        foreach ($endpoints as $endpoint) {
            $method = $endpoint['method'];
            $path = $endpoint['path'];
            $signature = "$method $path";

            $sdkMethod = $mapping[$signature] ?? null;

            if ($sdkMethod && isset(static::$sdkMethods[$sdkMethod])) {
                $mappedEndpoints++;

                if (isset($testedMethods[$sdkMethod])) {
                    $testedEndpoints++;
                }
            }
        }

        $this->addToAssertionCount(1);

        echo "\n\n";
        echo "=== Server Credentials API Coverage ===\n";
        echo "Total Endpoints: $totalEndpoints\n";
        echo "Mapped to SDK: $mappedEndpoints (".($totalEndpoints > 0 ? round($mappedEndpoints / $totalEndpoints * 100, 2) : 0)."%)\n";
        echo "With Tests: $testedEndpoints (".($totalEndpoints > 0 ? round($testedEndpoints / $totalEndpoints * 100, 2) : 0)."%)\n";
        echo str_repeat('=', strlen("Server Credentials API Coverage") + 8) . "\n\n";
    }
}
