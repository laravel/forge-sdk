<?php

declare(strict_types=1);

namespace Tests\OpenAPI;

use Laravel\Forge\Forge;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

/**
 * Base test case for OpenAPI compliance testing.
 *
 * This class provides utilities for dynamically testing that the Forge SDK
 * has complete coverage of all API v2 endpoints by comparing SDK methods
 * against the latest OpenAPI specification.
 */
abstract class OpenAPITestCase extends TestCase
{
    protected static array $openApiSpec;

    protected static array $allEndpoints;

    protected static array $sdkMethods;

    protected static array $testMethods;

    /**
     * Shared cache across all test classes in the same run.
     */
    private static ?array $sharedOpenApiSpec = null;

    private static ?array $sharedEndpoints = null;

    private static ?array $sharedSdkMethods = null;

    private static ?array $sharedTestMethods = null;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // Use shared cache if available (same test run)
        if (static::$sharedOpenApiSpec !== null) {
            static::$openApiSpec = static::$sharedOpenApiSpec;
            static::$allEndpoints = static::$sharedEndpoints;
            static::$sdkMethods = static::$sharedSdkMethods;
            static::$testMethods = static::$sharedTestMethods;

            return;
        }

        // Load and cache
        static::$openApiSpec = static::downloadOpenApiSpec();
        static::$allEndpoints = static::extractEndpoints(static::$openApiSpec);
        static::$sdkMethods = static::extractSdkMethods();
        static::$testMethods = static::extractTestMethods();

        // Store in shared cache
        static::$sharedOpenApiSpec = static::$openApiSpec;
        static::$sharedEndpoints = static::$allEndpoints;
        static::$sharedSdkMethods = static::$sdkMethods;
        static::$sharedTestMethods = static::$testMethods;
    }

    /**
     * Download the OpenAPI specification.
     * Uses local cache if available and recent (< 1 day old).
     */
    protected static function downloadOpenApiSpec(): array
    {
        $localPath = __DIR__.'/../../forge-openapi.json';

        // Check if local file exists and is recent (less than 1 day old)
        if (file_exists($localPath) && (time() - filemtime($localPath)) < 86400) {
            $spec = file_get_contents($localPath);
            $decoded = json_decode($spec, true);

            if ($decoded && json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

        // Download fresh copy
        $url = 'https://forge.laravel.com/api/docs.openapi';
        $spec = @file_get_contents($url);

        if ($spec === false) {
            // If download fails but we have a local file, use it
            if (file_exists($localPath)) {
                $spec = file_get_contents($localPath);
                $decoded = json_decode($spec, true);

                if ($decoded && json_last_error() === JSON_ERROR_NONE) {
                    return $decoded;
                }
            }

            static::fail("Failed to download OpenAPI spec from $url");
        }

        $decoded = json_decode($spec, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            static::fail('Failed to parse OpenAPI spec JSON: '.json_last_error_msg());
        }

        // Save it locally for next time
        file_put_contents($localPath, $spec);

        return $decoded;
    }

    /**
     * Extract all endpoints from the OpenAPI spec.
     */
    protected static function extractEndpoints(array $spec): array
    {
        $endpoints = [];

        foreach ($spec['paths'] as $path => $methods) {
            foreach ($methods as $method => $details) {
                if (in_array($method, ['get', 'post', 'put', 'patch', 'delete'])) {
                    $endpoints[] = [
                        'method' => strtoupper($method),
                        'path' => $path,
                        'operationId' => $details['operationId'] ?? null,
                        'summary' => $details['summary'] ?? null,
                        'tags' => $details['tags'] ?? [],
                        'responses' => $details['responses'] ?? [],
                    ];
                }
            }
        }

        return $endpoints;
    }

    /**
     * Extract all SDK methods from the Forge class.
     */
    protected static function extractSdkMethods(): array
    {
        $methods = [];

        // Get all methods from Forge class
        $reflection = new ReflectionClass(Forge::class);

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            // Skip inherited methods from base PHP classes
            if ($method->getDeclaringClass()->getName() === Forge::class ||
                $method->getDeclaringClass()->isSubclassOf(Forge::class) === false &&
                strpos($method->getDeclaringClass()->getName(), 'Laravel\\Forge') === 0) {

                $methods[$method->getName()] = [
                    'name' => $method->getName(),
                    'class' => $method->getDeclaringClass()->getName(),
                    'parameters' => $method->getNumberOfParameters(),
                    'file' => $method->getFileName(),
                    'line' => $method->getStartLine(),
                ];
            }
        }

        // Also scan traits used by Forge
        $traits = $reflection->getTraitNames();
        foreach ($traits as $traitName) {
            if (strpos($traitName, 'Laravel\\Forge\\Actions') === 0) {
                $traitReflection = new ReflectionClass($traitName);
                foreach ($traitReflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                    $methods[$method->getName()] = [
                        'name' => $method->getName(),
                        'class' => $traitName,
                        'parameters' => $method->getNumberOfParameters(),
                        'file' => $method->getFileName(),
                        'line' => $method->getStartLine(),
                    ];
                }
            }
        }

        return $methods;
    }

    /**
     * Extract test methods from the main ForgeSDKTest file.
     */
    protected static function extractTestMethods(): array
    {
        $testFile = __DIR__.'/../ForgeSDKTest.php';
        if (! file_exists($testFile)) {
            return [];
        }

        $content = file_get_contents($testFile);
        $methods = [];

        // Use token parsing to extract test methods with proper brace counting
        $tokens = token_get_all($content);
        $currentMethod = null;
        $braceLevel = 0;
        $methodCode = '';
        $inMethod = false;

        for ($i = 0; $i < count($tokens); $i++) {
            $token = $tokens[$i];

            // Look for "public function test_"
            if (is_array($token) && $token[0] === T_PUBLIC) {
                // Check if next tokens are "function test_"
                $j = $i + 1;
                while ($j < count($tokens) && is_array($tokens[$j]) && $tokens[$j][0] === T_WHITESPACE) {
                    $j++;
                }
                if ($j < count($tokens) && is_array($tokens[$j]) && $tokens[$j][0] === T_FUNCTION) {
                    $j++;
                    while ($j < count($tokens) && is_array($tokens[$j]) && $tokens[$j][0] === T_WHITESPACE) {
                        $j++;
                    }
                    if ($j < count($tokens) && is_array($tokens[$j]) && $tokens[$j][0] === T_STRING && strpos($tokens[$j][1], 'test_') === 0) {
                        $currentMethod = $tokens[$j][1];
                        $inMethod = true;
                        $braceLevel = 0;
                        $methodCode = '';
                    }
                }
            }

            if ($inMethod) {
                if ($token === '{') {
                    $braceLevel++;
                    if ($braceLevel > 1) {
                        $methodCode .= '{';
                    }
                } elseif ($token === '}') {
                    $braceLevel--;
                    if ($braceLevel === 0) {
                        // Method complete
                        $methods[$currentMethod] = $methodCode;
                        $inMethod = false;
                        $currentMethod = null;
                    } else {
                        $methodCode .= '}';
                    }
                } elseif ($braceLevel > 0) {
                    $methodCode .= is_array($token) ? $token[1] : $token;
                }
            }
        }

        return $methods;
    }

    /**
     * Get all endpoints for a specific tag.
     */
    protected static function getEndpointsForTag(string $tag): array
    {
        return array_filter(static::$allEndpoints, function ($endpoint) use ($tag) {
            return in_array($tag, $endpoint['tags']);
        });
    }

    /**
     * Get internal/helper methods that should be skipped in testing.
     */
    protected static function getInternalMethods(): array
    {
        return [
            'transformCollection', 'setApiKey', 'setTimeout', 'getTimeout',
            '__construct', 'get', 'post', 'put', 'patch', 'delete', 'retry',
        ];
    }

    /**
     * Get SDK methods that have tests.
     */
    protected static function getTestedMethods(): array
    {
        $testedMethods = [];

        // Extract method names being tested from test file
        foreach (static::$testMethods as $testName => $testCode) {
            // Look for patterns like $forge->methodName(
            if (preg_match_all('/\$forge->([a-zA-Z]+)\(/', $testCode, $matches)) {
                foreach ($matches[1] as $methodName) {
                    if ($methodName !== 'setApiKey') {
                        $testedMethods[$methodName] = true;
                    }
                }
            }
        }

        return $testedMethods;
    }
}
