<?php

declare(strict_types=1);

/**
 * Generate test classes for all OpenAPI categories.
 *
 * This script creates a test file for each tag/category in the OpenAPI spec.
 * Run with: php tests/OpenAPI/generate-tests.php
 */

$spec = json_decode(file_get_contents(__DIR__.'/../../forge-openapi.json'), true);
$tags = array_column($spec['tags'], 'name');

$template = <<<'PHP'
<?php

namespace Tests\OpenAPI;

/**
 * Tests OpenAPI compliance for {CATEGORY} endpoints.
 *
 * This test class dynamically validates that all {CATEGORY}-tagged endpoints
 * in the OpenAPI spec have corresponding SDK methods and tests.
 */
class {CLASS_NAME}Test extends OpenAPITestCase
{
    protected static string $category = '{CATEGORY}';

    public function test_all_{SLUG}_endpoints_have_sdk_methods()
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
                "The following ".count($uncovered)." {CATEGORY} endpoints do not have SDK methods:\n".
                implode("\n", $uncovered)
            );
        }

        $this->assertTrue(true);
    }

    public function test_all_{SLUG}_sdk_methods_have_tests()
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
                "The following ".count($missingTests)." {CATEGORY} SDK methods do not have tests:\n".
                implode("\n", $missingTests)
            );
        }

        $this->assertTrue(true);
    }

    public function test_{SLUG}_endpoint_coverage_statistics()
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
        echo "=== {CATEGORY} API Coverage ===\n";
        echo "Total Endpoints: $totalEndpoints\n";
        echo "Mapped to SDK: $mappedEndpoints (".($totalEndpoints > 0 ? round($mappedEndpoints / $totalEndpoints * 100, 2) : 0)."%)\n";
        echo "With Tests: $testedEndpoints (".($totalEndpoints > 0 ? round($testedEndpoints / $totalEndpoints * 100, 2) : 0)."%)\n";
        echo str_repeat('=', strlen("{CATEGORY} API Coverage") + 8) . "\n\n";
    }
}

PHP;

function slugify($text)
{
    return strtolower(str_replace([' ', '-'], '_', $text));
}

function pascalCase($text)
{
    return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $text)));
}

$generated = [];

foreach ($tags as $tag) {
    $className = pascalCase($tag);
    $slug = slugify($tag);

    $content = str_replace(
        ['{CATEGORY}', '{CLASS_NAME}', '{SLUG}'],
        [$tag, $className, $slug],
        $template
    );

    $filename = __DIR__."/{$className}Test.php";
    file_put_contents($filename, $content);

    $generated[] = "{$className}Test.php";
}

echo "Generated ".count($generated)." test files:\n";
foreach ($generated as $file) {
    echo "  - $file\n";
}
echo "\nDone!\n";
