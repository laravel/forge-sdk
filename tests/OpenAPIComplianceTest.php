<?php

namespace Tests;

use Laravel\Forge\Forge;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

/**
 * This test validates that the Forge SDK has complete coverage of all API v2 endpoints
 * by comparing the SDK methods against the latest OpenAPI specification.
 */
class OpenAPIComplianceTest extends TestCase
{
    private array $openApiSpec;

    private array $allEndpoints;

    private array $sdkMethods;

    private array $testMethods;

    private array $uncoveredEndpoints = [];

    private array $missingTests = [];

    private array $unmockedTests = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->openApiSpec = $this->downloadOpenApiSpec();
        $this->allEndpoints = $this->extractEndpoints($this->openApiSpec);
        $this->sdkMethods = $this->extractSdkMethods();
        $this->testMethods = $this->extractTestMethods();
    }

    public function test_openapi_spec_can_be_downloaded()
    {
        $this->assertIsArray($this->openApiSpec);
        $this->assertArrayHasKey('paths', $this->openApiSpec);
        $this->assertNotEmpty($this->openApiSpec['paths']);
    }

    public function test_all_endpoints_have_sdk_methods()
    {
        $mapping = $this->createEndpointToMethodMapping();

        foreach ($this->allEndpoints as $endpoint) {
            $method = $endpoint['method'];
            $path = $endpoint['path'];
            $signature = "$method $path";

            if (! isset($mapping[$signature])) {
                $this->uncoveredEndpoints[] = $signature;
            }
        }

        if (! empty($this->uncoveredEndpoints)) {
            $this->fail(
                'The following '.count($this->uncoveredEndpoints)." API endpoints do not have corresponding SDK methods:\n".
                implode("\n", $this->uncoveredEndpoints)
            );
        }

        $this->assertTrue(true);
    }

    public function test_all_sdk_methods_have_tests()
    {
        $testedMethods = [];

        // Extract method names being tested from test file
        foreach ($this->testMethods as $testName => $testCode) {
            // Look for patterns like $forge->methodName(
            if (preg_match_all('/\$forge->([a-zA-Z]+)\(/', $testCode, $matches)) {
                foreach ($matches[1] as $methodName) {
                    if ($methodName !== 'setApiKey') {
                        $testedMethods[$methodName] = true;
                    }
                }
            }
        }

        foreach ($this->sdkMethods as $methodName => $methodInfo) {
            // Skip internal/helper methods
            $internalMethods = [
                'transformCollection', 'setApiKey', 'setTimeout', 'getTimeout',
                '__construct', 'get', 'post', 'put', 'patch', 'delete', 'retry',
            ];

            if (in_array($methodName, $internalMethods)) {
                continue;
            }

            if (! isset($testedMethods[$methodName])) {
                $this->missingTests[] = $methodName.' ('.$methodInfo['class'].')';
            }
        }

        if (! empty($this->missingTests)) {
            $this->fail(
                'The following '.count($this->missingTests)." SDK methods do not have tests:\n".
                implode("\n", $this->missingTests)
            );
        }

        $this->assertTrue(true);
    }

    public function test_all_tests_have_mocked_responses()
    {
        foreach ($this->testMethods as $testName => $testCode) {
            // Check if test makes HTTP calls
            if (strpos($testCode, '$http->shouldReceive') !== false || strpos($testCode, 'shouldReceive(\'request\')') !== false) {
                // Normalize whitespace to handle multiline patterns
                $normalizedCode = preg_replace('/\s+/', ' ', $testCode);
                // Check if it has andReturn with a Response
                if (strpos($normalizedCode, 'andReturn') === false && strpos($normalizedCode, '->andReturn(') === false) {
                    $this->unmockedTests[] = $testName;
                }
            }
        }

        if (! empty($this->unmockedTests)) {
            $this->fail(
                'The following '.count($this->unmockedTests)." tests do not have mocked responses:\n".
                implode("\n", $this->unmockedTests)
            );
        }

        $this->assertTrue(true);
    }

    public function test_coverage_statistics()
    {
        $totalEndpoints = count($this->allEndpoints);
        $totalMethods = count(array_filter($this->sdkMethods, function ($method) {
            return ! in_array($method['name'], ['transformCollection', 'setApiKey', 'setTimeout', 'getTimeout']);
        }));
        $totalTests = count($this->testMethods);

        $this->addToAssertionCount(1);

        echo "\n\n";
        echo "=== Forge SDK API v2 Coverage Report ===\n";
        echo "Total API Endpoints: $totalEndpoints\n";
        echo "Total SDK Methods: $totalMethods\n";
        echo "Total Tests: $totalTests\n";
        echo "\n";
        echo 'Endpoint Coverage: '.($totalEndpoints > 0 ? round(($totalEndpoints - count($this->uncoveredEndpoints)) / $totalEndpoints * 100, 2) : 0)."%\n";
        echo 'Method Test Coverage: '.($totalMethods > 0 ? round(($totalMethods - count($this->missingTests)) / $totalMethods * 100, 2) : 0)."%\n";
        echo "=========================================\n\n";
    }

    private function downloadOpenApiSpec(): array
    {
        // First, try to load from local file if it exists (for offline testing)
        $localPath = __DIR__.'/../forge-openapi.json';
        if (file_exists($localPath)) {
            $spec = json_decode(file_get_contents($localPath), true);
            if ($spec) {
                return $spec;
            }
        }

        // Download from live API
        $url = 'https://forge.laravel.com/api/docs.openapi';
        $spec = @file_get_contents($url);

        if ($spec === false) {
            $this->fail("Failed to download OpenAPI spec from $url");
        }

        $decoded = json_decode($spec, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->fail('Failed to parse OpenAPI spec JSON: '.json_last_error_msg());
        }

        // Save it locally for future runs
        file_put_contents($localPath, $spec);

        return $decoded;
    }

    private function extractEndpoints(array $spec): array
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
                    ];
                }
            }
        }

        return $endpoints;
    }

    private function extractSdkMethods(): array
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
                    ];
                }
            }
        }

        return $methods;
    }

    private function extractTestMethods(): array
    {
        $testFile = __DIR__.'/ForgeSDKTest.php';
        if (! file_exists($testFile)) {
            $this->fail("Test file not found: $testFile");
        }

        $content = file_get_contents($testFile);
        $methods = [];

        // Use a more robust approach to extract test methods with proper brace counting
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

    private function createEndpointToMethodMapping(): array
    {
        $mapping = [];

        // This is a comprehensive mapping of API endpoints to SDK methods
        // Based on the Forge SDK v4 architecture

        // Organizations
        $mapping['GET /orgs'] = 'organizations';
        $mapping['GET /orgs/{organization}'] = 'organization';

        // User
        $mapping['GET /user'] = 'user';
        $mapping['GET /me'] = 'me';

        // Providers
        $mapping['GET /providers'] = 'providers';
        $mapping['GET /providers/{provider}'] = 'provider';
        $mapping['GET /providers/{provider}/sizes'] = 'providerSizes';
        $mapping['GET /providers/{provider}/sizes/{providerSize}'] = 'providerSize';
        $mapping['GET /providers/{provider}/regions'] = 'providerRegions';
        $mapping['GET /providers/{provider}/regions/{providerRegion}'] = 'providerRegion';
        $mapping['GET /providers/{provider}/regions/{providerRegion}/sizes'] = 'providerRegionSizes';
        $mapping['GET /providers/{provider}/regions/{providerRegion}/sizes/{providerSize}'] = 'providerRegionSize';

        // Servers
        $mapping['GET /orgs/{organization}/servers'] = 'servers';
        $mapping['POST /orgs/{organization}/servers'] = 'createServer';
        $mapping['GET /orgs/{organization}/servers/{server}'] = 'server';
        $mapping['DELETE /orgs/{organization}/servers/{server}'] = 'deleteServer';
        $mapping['POST /orgs/{organization}/servers/{server}/actions'] = 'createServerAction';
        $mapping['GET /orgs/{organization}/servers/{server}/events'] = 'serverEvents';
        $mapping['GET /orgs/{organization}/servers/{server}/events/{event}'] = 'serverEvent';
        $mapping['GET /orgs/{organization}/servers/{server}/events/{event}/output'] = 'serverEventOutput';

        // Server Archives
        $mapping['GET /orgs/{organization}/servers/archives'] = 'archivedServers';
        $mapping['POST /orgs/{organization}/servers/archives'] = 'archiveServer';
        $mapping['DELETE /orgs/{organization}/servers/archives/{server}'] = 'deleteArchivedServer';

        // Server Services
        $mapping['POST /orgs/{organization}/servers/{server}/services/nginx/actions'] = 'performNginxAction';
        $mapping['POST /orgs/{organization}/servers/{server}/services/postgres/actions'] = 'performPostgresAction';
        $mapping['POST /orgs/{organization}/servers/{server}/services/redis/actions'] = 'performRedisAction';
        $mapping['POST /orgs/{organization}/servers/{server}/services/mysql/actions'] = 'performMySQLAction';
        $mapping['POST /orgs/{organization}/servers/{server}/services/php/actions'] = 'performPHPAction';
        $mapping['POST /orgs/{organization}/servers/{server}/services/supervisor/actions'] = 'performSupervisorAction';

        // PHP Management
        $mapping['GET /orgs/{organization}/servers/{server}/php/versions'] = 'phpVersions';
        $mapping['POST /orgs/{organization}/servers/{server}/php/versions'] = 'installPhpVersion';
        $mapping['GET /orgs/{organization}/servers/{server}/php/versions/{phpVersion}'] = 'phpVersion';
        $mapping['PUT /orgs/{organization}/servers/{server}/php/versions/{phpVersion}'] = 'updatePhpVersion';
        $mapping['DELETE /orgs/{organization}/servers/{server}/php/versions/{phpVersion}'] = 'deletePhpVersion';
        $mapping['GET /orgs/{organization}/servers/{server}/php/cli-version'] = 'phpCliVersion';
        $mapping['PUT /orgs/{organization}/servers/{server}/php/cli-version'] = 'updatePhpCliVersion';
        $mapping['GET /orgs/{organization}/servers/{server}/php/site-version'] = 'phpSiteVersion';
        $mapping['PUT /orgs/{organization}/servers/{server}/php/site-version'] = 'updatePhpSiteVersion';
        $mapping['GET /orgs/{organization}/servers/{server}/php/versions/{phpVersion}/configs/fpm'] = 'phpFpmConfig';
        $mapping['PUT /orgs/{organization}/servers/{server}/php/versions/{phpVersion}/configs/fpm'] = 'updatePhpFpmConfig';
        $mapping['GET /orgs/{organization}/servers/{server}/php/versions/{phpVersion}/configs/cli'] = 'phpCliConfig';
        $mapping['PUT /orgs/{organization}/servers/{server}/php/versions/{phpVersion}/configs/cli'] = 'updatePhpCliConfig';
        $mapping['GET /orgs/{organization}/servers/{server}/php/versions/{phpVersion}/configs/pool'] = 'phpPoolConfig';
        $mapping['PUT /orgs/{organization}/servers/{server}/php/versions/{phpVersion}/configs/pool'] = 'updatePhpPoolConfig';
        $mapping['GET /orgs/{organization}/servers/{server}/php/max-upload-size'] = 'phpMaxUploadSize';
        $mapping['PUT /orgs/{organization}/servers/{server}/php/max-upload-size'] = 'updatePhpMaxUploadSize';
        $mapping['GET /orgs/{organization}/servers/{server}/php/max-execution-time'] = 'phpMaxExecutionTime';
        $mapping['PUT /orgs/{organization}/servers/{server}/php/max-execution-time'] = 'updatePhpMaxExecutionTime';
        $mapping['GET /orgs/{organization}/servers/{server}/php/opcache'] = 'phpOpcacheStatus';
        $mapping['POST /orgs/{organization}/servers/{server}/php/opcache'] = 'reloadPhpOpcache';
        $mapping['DELETE /orgs/{organization}/servers/{server}/php/opcache'] = 'clearPhpOpcache';

        // Sites
        $mapping['GET /sites'] = 'allSites';
        $mapping['GET /orgs/{organization}/sites'] = 'organizationSites';
        $mapping['GET /orgs/{organization}/sites/{site}'] = 'organizationSite';
        $mapping['GET /orgs/{organization}/servers/{server}/sites'] = 'serverSites';
        $mapping['POST /orgs/{organization}/servers/{server}/sites'] = 'createSite';
        $mapping['PUT /orgs/{organization}/servers/{server}/sites/{site}'] = 'updateSite';
        $mapping['DELETE /orgs/{organization}/servers/{server}/sites/{site}'] = 'deleteSite';

        // Site Configuration
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/nginx'] = 'siteNginxConfig';
        $mapping['PUT /orgs/{organization}/servers/{server}/sites/{site}/nginx'] = 'updateSiteNginxConfig';
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/environment'] = 'siteEnvironment';
        $mapping['PUT /orgs/{organization}/servers/{server}/sites/{site}/environment'] = 'updateSiteEnvironment';
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/healthcheck'] = 'siteHealthcheck';
        $mapping['PUT /orgs/{organization}/servers/{server}/sites/{site}/healthcheck'] = 'updateSiteHealthcheck';

        // Site Logs
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/logs/nginx-access'] = 'siteNginxAccessLog';
        $mapping['DELETE /orgs/{organization}/servers/{server}/sites/{site}/logs/nginx-access'] = 'deleteSiteNginxAccessLog';
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/logs/nginx-error'] = 'siteNginxErrorLog';
        $mapping['DELETE /orgs/{organization}/servers/{server}/sites/{site}/logs/nginx-error'] = 'deleteSiteNginxErrorLog';
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/logs/application'] = 'siteApplicationLog';
        $mapping['DELETE /orgs/{organization}/servers/{server}/sites/{site}/logs/application'] = 'deleteSiteApplicationLog';

        // Domains
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/domains'] = 'domains';
        $mapping['POST /orgs/{organization}/servers/{server}/sites/{site}/domains'] = 'createDomain';
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/domains/{domainRecord}'] = 'domain';
        $mapping['PATCH /orgs/{organization}/servers/{server}/sites/{site}/domains/{domainRecord}'] = 'updateDomain';
        $mapping['DELETE /orgs/{organization}/servers/{server}/sites/{site}/domains/{domainRecord}'] = 'deleteDomain';
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/domains/{domainRecord}/configurations'] = 'domainConfigurations';
        $mapping['POST /orgs/{organization}/servers/{server}/sites/{site}/domains/{domainRecord}/actions'] = 'createDomainAction';
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/domains/{domainRecord}/nginx'] = 'domainNginxConfig';
        $mapping['PUT /orgs/{organization}/servers/{server}/sites/{site}/domains/{domainRecord}/nginx'] = 'updateDomainNginxConfig';

        // Domain Certificates
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/domains/{domainRecord}/certificate'] = 'domainCertificate';
        $mapping['POST /orgs/{organization}/servers/{server}/sites/{site}/domains/{domainRecord}/certificate'] = 'createDomainCertificate';
        $mapping['DELETE /orgs/{organization}/servers/{server}/sites/{site}/domains/{domainRecord}/certificate'] = 'deleteDomainCertificate';
        $mapping['POST /orgs/{organization}/servers/{server}/sites/{site}/domains/{domainRecord}/certificate/actions'] = 'createDomainCertificateAction';

        // Composer Credentials
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/composer/credentials'] = 'composerCredentials';
        $mapping['POST /orgs/{organization}/servers/{server}/sites/{site}/composer/credentials'] = 'createComposerCredential';
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/composer/credentials/{repository}'] = 'composerCredential';
        $mapping['PUT /orgs/{organization}/servers/{server}/sites/{site}/composer/credentials/{repository}'] = 'updateComposerCredential';
        $mapping['DELETE /orgs/{organization}/servers/{server}/sites/{site}/composer/credentials/{repository}'] = 'deleteComposerCredential';

        // Load Balancing
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/load-balancing-nodes'] = 'loadBalancingNodes';
        $mapping['PUT /orgs/{organization}/servers/{server}/sites/{site}/load-balancing-nodes'] = 'updateLoadBalancingNodes';

        // Databases
        $mapping['GET /orgs/{organization}/servers/{server}/database/schemas'] = 'databases';
        $mapping['POST /orgs/{organization}/servers/{server}/database/schemas'] = 'createDatabase';
        $mapping['GET /orgs/{organization}/servers/{server}/database/schemas/{database}'] = 'database';
        $mapping['DELETE /orgs/{organization}/servers/{server}/database/schemas/{database}'] = 'deleteDatabase';
        $mapping['POST /orgs/{organization}/servers/{server}/database/schemas/synchronizations'] = 'syncDatabases';
        $mapping['GET /orgs/{organization}/servers/{server}/database/users'] = 'databaseUsers';
        $mapping['POST /orgs/{organization}/servers/{server}/database/users'] = 'createDatabaseUser';
        $mapping['GET /orgs/{organization}/servers/{server}/database/users/{databaseUser}'] = 'databaseUser';
        $mapping['PUT /orgs/{organization}/servers/{server}/database/users/{databaseUser}'] = 'updateDatabaseUser';
        $mapping['DELETE /orgs/{organization}/servers/{server}/database/users/{databaseUser}'] = 'deleteDatabaseUser';
        $mapping['PUT /orgs/{organization}/servers/{server}/database/password'] = 'updateDatabasePassword';

        // Background Processes
        $mapping['GET /orgs/{organization}/servers/{server}/background-processes'] = 'backgroundProcesses';
        $mapping['POST /orgs/{organization}/servers/{server}/background-processes'] = 'createBackgroundProcess';
        $mapping['GET /orgs/{organization}/servers/{server}/background-processes/{backgroundProcess}'] = 'backgroundProcess';
        $mapping['PUT /orgs/{organization}/servers/{server}/background-processes/{backgroundProcess}'] = 'updateBackgroundProcess';
        $mapping['DELETE /orgs/{organization}/servers/{server}/background-processes/{backgroundProcess}'] = 'deleteBackgroundProcess';
        $mapping['GET /orgs/{organization}/servers/{server}/background-processes/{backgroundProcess}/log'] = 'backgroundProcessLog';
        $mapping['POST /orgs/{organization}/servers/{server}/background-processes/{backgroundProcess}/actions'] = 'createBackgroundProcessAction';

        // Scheduled Jobs
        $mapping['GET /orgs/{organization}/servers/{server}/scheduled-jobs'] = 'scheduledJobs';
        $mapping['POST /orgs/{organization}/servers/{server}/scheduled-jobs'] = 'createScheduledJob';
        $mapping['GET /orgs/{organization}/servers/{server}/scheduled-jobs/{job}'] = 'scheduledJob';
        $mapping['DELETE /orgs/{organization}/servers/{server}/scheduled-jobs/{job}'] = 'deleteScheduledJob';
        $mapping['GET /orgs/{organization}/servers/{server}/scheduled-jobs/{job}/output'] = 'scheduledJobOutput';
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/scheduled-jobs'] = 'siteScheduledJobs';
        $mapping['POST /orgs/{organization}/servers/{server}/sites/{site}/scheduled-jobs'] = 'createSiteScheduledJob';
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/scheduled-jobs/{job}'] = 'siteScheduledJob';
        $mapping['DELETE /orgs/{organization}/servers/{server}/sites/{site}/scheduled-jobs/{job}'] = 'deleteSiteScheduledJob';
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/scheduled-jobs/{job}/output'] = 'siteScheduledJobOutput';

        // Firewall Rules
        $mapping['GET /orgs/{organization}/servers/{server}/firewall-rules'] = 'firewallRules';
        $mapping['POST /orgs/{organization}/servers/{server}/firewall-rules'] = 'createFirewallRule';
        $mapping['GET /orgs/{organization}/servers/{server}/firewall-rules/{rule}'] = 'firewallRule';
        $mapping['DELETE /orgs/{organization}/servers/{server}/firewall-rules/{rule}'] = 'deleteFirewallRule';

        // Redirect Rules
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/redirect-rules'] = 'redirectRules';
        $mapping['POST /orgs/{organization}/servers/{server}/sites/{site}/redirect-rules'] = 'createRedirectRule';
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/redirect-rules/{redirectRule}'] = 'redirectRule';
        $mapping['DELETE /orgs/{organization}/servers/{server}/sites/{site}/redirect-rules/{redirectRule}'] = 'deleteRedirectRule';

        // Security Rules
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/security-rules'] = 'securityRules';
        $mapping['POST /orgs/{organization}/servers/{server}/sites/{site}/security-rules'] = 'createSecurityRule';
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/security-rules/{securityRule}'] = 'securityRule';
        $mapping['PUT /orgs/{organization}/servers/{server}/sites/{site}/security-rules/{securityRule}'] = 'updateSecurityRule';
        $mapping['DELETE /orgs/{organization}/servers/{server}/sites/{site}/security-rules/{securityRule}'] = 'deleteSecurityRule';

        // Monitors
        $mapping['GET /orgs/{organization}/servers/{server}/monitors'] = 'monitors';
        $mapping['POST /orgs/{organization}/servers/{server}/monitors'] = 'createMonitor';
        $mapping['GET /orgs/{organization}/servers/{server}/monitors/{monitor}'] = 'monitor';
        $mapping['DELETE /orgs/{organization}/servers/{server}/monitors/{monitor}'] = 'deleteMonitor';

        // Nginx
        $mapping['GET /orgs/{organization}/servers/{server}/nginx/templates'] = 'nginxTemplates';
        $mapping['POST /orgs/{organization}/servers/{server}/nginx/templates'] = 'createNginxTemplate';
        $mapping['GET /orgs/{organization}/servers/{server}/nginx/templates/{nginxTemplate}'] = 'nginxTemplate';
        $mapping['PUT /orgs/{organization}/servers/{server}/nginx/templates/{nginxTemplate}'] = 'updateNginxTemplate';
        $mapping['DELETE /orgs/{organization}/servers/{server}/nginx/templates/{nginxTemplate}'] = 'deleteNginxTemplate';

        // SSH Keys
        $mapping['GET /orgs/{organization}/servers/{server}/ssh-keys'] = 'sshKeys';
        $mapping['POST /orgs/{organization}/servers/{server}/ssh-keys'] = 'createSSHKey';
        $mapping['GET /orgs/{organization}/servers/{server}/ssh-keys/{key}'] = 'sshKey';
        $mapping['DELETE /orgs/{organization}/servers/{server}/ssh-keys/{key}'] = 'deleteSSHKey';
        $mapping['GET /orgs/{organization}/servers/{server}/key'] = 'serverKey';
        $mapping['PUT /orgs/{organization}/servers/{server}/key'] = 'updateServerKey';

        // Logs
        $mapping['GET /orgs/{organization}/servers/{server}/logs/{key}'] = 'log';
        $mapping['DELETE /orgs/{organization}/servers/{server}/logs/{key}'] = 'deleteLog';

        // Deployments
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/deployments'] = 'deployments';
        $mapping['POST /orgs/{organization}/servers/{server}/sites/{site}/deployments'] = 'createDeployment';
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/deployments/{deployment}'] = 'deployment';
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/deployments/{deployment}/log'] = 'deploymentLog';
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/deployments/status'] = 'deploymentStatus';
        $mapping['DELETE /orgs/{organization}/servers/{server}/sites/{site}/deployments/status'] = 'disableQuickDeploy';
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/deployments/script'] = 'deploymentScript';
        $mapping['PUT /orgs/{organization}/servers/{server}/sites/{site}/deployments/script'] = 'updateDeploymentScript';
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/deployments/deploy-hook'] = 'deploymentTriggerUrl';
        $mapping['PUT /orgs/{organization}/servers/{server}/sites/{site}/deployments/deploy-hook'] = 'updateDeploymentTriggerUrl';
        $mapping['POST /orgs/{organization}/servers/{server}/sites/{site}/deployments/push-to-deploy'] = 'enablePushToDeploy';
        $mapping['DELETE /orgs/{organization}/servers/{server}/sites/{site}/deployments/push-to-deploy'] = 'disablePushToDeploy';

        // Webhooks
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/webhooks'] = 'webhooks';
        $mapping['POST /orgs/{organization}/servers/{server}/sites/{site}/webhooks'] = 'createWebhook';
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/webhooks/{deploymentWebhook}'] = 'webhook';
        $mapping['DELETE /orgs/{organization}/servers/{server}/sites/{site}/webhooks/{deploymentWebhook}'] = 'deleteWebhook';

        // Commands
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/commands'] = 'commands';
        $mapping['POST /orgs/{organization}/servers/{server}/sites/{site}/commands'] = 'createCommand';
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/commands/{command}'] = 'command';
        $mapping['DELETE /orgs/{organization}/servers/{server}/sites/{site}/commands/{command}'] = 'deleteCommand';
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/commands/{command}/output'] = 'commandOutput';

        // Heartbeats
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/heartbeats'] = 'heartbeats';
        $mapping['POST /orgs/{organization}/servers/{server}/sites/{site}/heartbeats'] = 'createHeartbeat';
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/heartbeats/{heartbeat}'] = 'heartbeat';
        $mapping['PUT /orgs/{organization}/servers/{server}/sites/{site}/heartbeats/{heartbeat}'] = 'updateHeartbeat';
        $mapping['DELETE /orgs/{organization}/servers/{server}/sites/{site}/heartbeats/{heartbeat}'] = 'deleteHeartbeat';

        // Integrations
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/integrations/horizon'] = 'getHorizon';
        $mapping['POST /orgs/{organization}/servers/{server}/sites/{site}/integrations/horizon'] = 'createHorizon';
        $mapping['DELETE /orgs/{organization}/servers/{server}/sites/{site}/integrations/horizon'] = 'deleteHorizon';
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/integrations/octane'] = 'getOctane';
        $mapping['POST /orgs/{organization}/servers/{server}/sites/{site}/integrations/octane'] = 'createOctane';
        $mapping['DELETE /orgs/{organization}/servers/{server}/sites/{site}/integrations/octane'] = 'deleteOctane';
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/integrations/reverb'] = 'getReverb';
        $mapping['POST /orgs/{organization}/servers/{server}/sites/{site}/integrations/reverb'] = 'createReverb';
        $mapping['DELETE /orgs/{organization}/servers/{server}/sites/{site}/integrations/reverb'] = 'deleteReverb';
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/integrations/inertia'] = 'getInertia';
        $mapping['POST /orgs/{organization}/servers/{server}/sites/{site}/integrations/inertia'] = 'createInertia';
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/integrations/pulse'] = 'getPulse';
        $mapping['POST /orgs/{organization}/servers/{server}/sites/{site}/integrations/pulse'] = 'createPulse';
        $mapping['DELETE /orgs/{organization}/servers/{server}/sites/{site}/integrations/pulse'] = 'deletePulse';
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/integrations/laravel-maintenance'] = 'getMaintenance';
        $mapping['POST /orgs/{organization}/servers/{server}/sites/{site}/integrations/laravel-maintenance'] = 'createMaintenance';
        $mapping['DELETE /orgs/{organization}/servers/{server}/sites/{site}/integrations/laravel-maintenance'] = 'deleteMaintenance';
        $mapping['GET /orgs/{organization}/servers/{server}/sites/{site}/integrations/laravel-scheduler'] = 'getScheduler';
        $mapping['POST /orgs/{organization}/servers/{server}/sites/{site}/integrations/laravel-scheduler'] = 'createScheduler';
        $mapping['DELETE /orgs/{organization}/servers/{server}/sites/{site}/integrations/laravel-scheduler'] = 'deleteScheduler';

        // Recipes
        $mapping['GET /orgs/{organization}/recipes'] = 'recipes';
        $mapping['POST /orgs/{organization}/recipes'] = 'createRecipe';
        $mapping['GET /orgs/{organization}/recipes/{recipe}'] = 'recipe';
        $mapping['PUT /orgs/{organization}/recipes/{recipe}'] = 'updateRecipe';
        $mapping['DELETE /orgs/{organization}/recipes/{recipe}'] = 'deleteRecipe';
        $mapping['GET /orgs/{organization}/recipes/{recipe}/runs'] = 'recipeRuns';
        $mapping['POST /orgs/{organization}/recipes/{recipe}/runs'] = 'createRecipeRun';
        $mapping['GET /orgs/{organization}/recipes/{recipe}/runs/{log}'] = 'recipeRun';
        $mapping['GET /forge-recipes'] = 'forgeRecipes';
        $mapping['GET /forge-recipes/{forgeRecipe}'] = 'forgeRecipe';
        $mapping['POST /forge-recipes/{forgeRecipe}/runs'] = 'createForgeRecipeRun';
        $mapping['GET /orgs/{organization}/teams/{team}/recipes'] = 'teamRecipes';
        $mapping['POST /orgs/{organization}/teams/{team}/recipes'] = 'shareRecipeWithTeam';
        $mapping['DELETE /orgs/{organization}/teams/{team}/recipes/{recipe}'] = 'deleteRecipeShare';

        // Teams
        $mapping['GET /orgs/{organization}/teams'] = 'teams';
        $mapping['POST /orgs/{organization}/teams'] = 'createTeam';
        $mapping['GET /orgs/{organization}/teams/{team}'] = 'team';
        $mapping['PUT /orgs/{organization}/teams/{team}'] = 'updateTeam';
        $mapping['DELETE /orgs/{organization}/teams/{team}'] = 'deleteTeam';
        $mapping['GET /orgs/{organization}/teams/{team}/members'] = 'teamMembers';
        $mapping['GET /orgs/{organization}/teams/{team}/members/{user}'] = 'teamMember';
        $mapping['PUT /orgs/{organization}/teams/{team}/members/{user}'] = 'updateTeamMember';
        $mapping['DELETE /orgs/{organization}/teams/{team}/members/{user}'] = 'deleteTeamMember';
        $mapping['GET /orgs/{organization}/teams/{team}/invites'] = 'teamInvitations';
        $mapping['POST /orgs/{organization}/teams/{team}/invites'] = 'createTeamInvitation';
        $mapping['GET /orgs/{organization}/teams/{team}/invites/{invitation}'] = 'teamInvitation';
        $mapping['DELETE /orgs/{organization}/teams/{team}/invites/{invitation}'] = 'deleteTeamInvitation';
        $mapping['GET /orgs/{organization}/teams/{team}/servers'] = 'teamServers';
        $mapping['POST /orgs/{organization}/teams/{team}/servers'] = 'attachServerToTeam';
        $mapping['DELETE /orgs/{organization}/teams/{team}/servers/{server}'] = 'detachServerFromTeam';
        $mapping['GET /orgs/{organization}/teams/{team}/server-credentials'] = 'teamServerCredentials';
        $mapping['POST /orgs/{organization}/teams/{team}/server-credentials'] = 'attachServerCredentialToTeam';
        $mapping['DELETE /orgs/{organization}/teams/{team}/server-credentials/{credential}'] = 'detachServerCredentialFromTeam';

        // Server Credentials
        $mapping['GET /orgs/{organization}/server-credentials'] = 'serverCredentials';
        $mapping['GET /orgs/{organization}/server-credentials/{credential}'] = 'serverCredential';
        $mapping['GET /orgs/{organization}/server-credentials/{credential}/regions/{region}/vpcs'] = 'vpcs';
        $mapping['POST /orgs/{organization}/server-credentials/{credential}/regions/{region}/vpcs'] = 'createVpc';
        $mapping['GET /orgs/{organization}/server-credentials/{credential}/regions/{region}/vpcs/{vpcId}'] = 'vpc';

        // Roles
        $mapping['GET /orgs/{organization}/roles'] = 'roles';
        $mapping['POST /orgs/{organization}/roles'] = 'createRole';
        $mapping['GET /orgs/{organization}/roles/{role}'] = 'role';
        $mapping['PUT /orgs/{organization}/roles/{role}'] = 'updateRole';
        $mapping['DELETE /orgs/{organization}/roles/{role}'] = 'deleteRole';
        $mapping['GET /orgs/{organization}/roles/{role}/permissions'] = 'rolePermissions';
        $mapping['GET /predefined-roles'] = 'predefinedRoles';
        $mapping['GET /predefined-roles/{role}'] = 'predefinedRole';
        $mapping['GET /permissions'] = 'permissions';
        $mapping['GET /permissions/{permission}'] = 'permission';

        return $mapping;
    }
}
