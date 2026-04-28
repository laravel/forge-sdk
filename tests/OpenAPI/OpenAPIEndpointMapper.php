<?php

declare(strict_types=1);

namespace Tests\OpenAPI;

/**
 * Maps OpenAPI endpoints to SDK methods using conventions.
 *
 * This class uses the MethodNameConventions system to automatically map
 * API endpoints to their corresponding SDK methods without hardcoded mappings.
 */
class OpenAPIEndpointMapper
{
    /**
     * Minimal special cases that don't follow any convention.
     * Keep this list as small as possible - prefer updating conventions over adding special cases.
     */
    protected static array $specialCases = [
        'GET /user' => 'user',
        'GET /me' => 'me',
        'GET /orgs' => 'organizations',
        'GET /orgs/{organization}' => 'organization',
        'GET /sites' => 'sites',
        'GET /orgs/{organization}/sites' => 'organizationSites',
        'GET /orgs/{organization}/sites/{site}' => 'organizationSite',
        'GET /forge-recipes' => 'forgeRecipes',
        'GET /forge-recipes/{forgeRecipe}' => 'forgeRecipe',
        'POST /forge-recipes/{forgeRecipe}/runs' => 'createForgeRecipeRun',
        'GET /predefined-roles' => 'predefinedRoles',
        'GET /predefined-roles/{role}' => 'predefinedRole',
        'GET /permissions' => 'permissions',
        'GET /permissions/{permission}' => 'permission',
        'GET /providers' => 'providers',
        'GET /providers/{provider}' => 'provider',
        'GET /providers/{provider}/sizes' => 'providerSizes',
        'GET /providers/{provider}/sizes/{providerSize}' => 'providerSize',
        'GET /providers/{provider}/regions' => 'providerRegions',
        'GET /providers/{provider}/regions/{providerRegion}' => 'providerRegion',
        'GET /providers/{provider}/regions/{providerRegion}/sizes' => 'providerRegionSizes',
        'GET /providers/{provider}/regions/{providerRegion}/sizes/{providerSize}' => 'providerRegionSize',
        'GET /orgs/{organization}/server-credentials/{credential}/regions/{region}/vpcs' => 'vpcs',
        'POST /orgs/{organization}/server-credentials/{credential}/regions/{region}/vpcs' => 'createVpc',
        'GET /orgs/{organization}/server-credentials/{credential}/regions/{region}/vpcs/{vpcId}' => 'vpc',
        'POST /orgs/{organization}/servers/archives' => 'createArchivedServer',
        // Background Processes
        'GET /orgs/{organization}/servers/{server}/background-processes/{backgroundProcess}/log' => 'backgroundProcessLog',
        // Backups - backup configurations and instances
        'GET /orgs/{organization}/servers/{server}/database/backups' => 'backupConfigurations',
        'POST /orgs/{organization}/servers/{server}/database/backups' => 'createBackupConfiguration',
        'GET /orgs/{organization}/servers/{server}/database/backups/{backupConfiguration}' => 'backupConfiguration',
        'PUT /orgs/{organization}/servers/{server}/database/backups/{backupConfiguration}' => 'updateBackupConfiguration',
        'DELETE /orgs/{organization}/servers/{server}/database/backups/{backupConfiguration}' => 'deleteBackupConfiguration',
        'GET /orgs/{organization}/servers/{server}/database/backups/{backupConfiguration}/instances' => 'backups',
        'POST /orgs/{organization}/servers/{server}/database/backups/{backupConfiguration}/instances' => 'createBackup',
        'GET /orgs/{organization}/servers/{server}/database/backups/{backupConfiguration}/instances/{backup}' => 'backup',
        'DELETE /orgs/{organization}/servers/{server}/database/backups/{backupConfiguration}/instances/{backup}' => 'deleteBackup',
        'POST /orgs/{organization}/servers/{server}/database/backups/{backupConfiguration}/instances/{backup}/restores' => 'restoreBackup',
        // Databases - 'database' is a namespace, 'schemas' is the resource
        'GET /orgs/{organization}/servers/{server}/database/schemas' => 'databases',
        'POST /orgs/{organization}/servers/{server}/database/schemas' => 'createDatabase',
        'GET /orgs/{organization}/servers/{server}/database/schemas/{database}' => 'database',
        'DELETE /orgs/{organization}/servers/{server}/database/schemas/{database}' => 'deleteDatabase',
        'POST /orgs/{organization}/servers/{server}/database/schemas/synchronizations' => 'syncDatabases',
        // Deployments - special deployment-related endpoints
        'GET /orgs/{organization}/servers/{server}/sites/{site}/deployments/{deployment}/log' => 'deploymentLog',
        'GET /orgs/{organization}/servers/{server}/sites/{site}/deployments/status' => 'deploymentStatus',
        'DELETE /orgs/{organization}/servers/{server}/sites/{site}/deployments/status' => 'disableQuickDeploy',
        'GET /orgs/{organization}/servers/{server}/sites/{site}/deployments/script' => 'deploymentScript',
        'PUT /orgs/{organization}/servers/{server}/sites/{site}/deployments/script' => 'updateDeploymentScript',
        'GET /orgs/{organization}/servers/{server}/sites/{site}/deployments/deploy-hook' => 'deploymentTriggerUrl',
        'PUT /orgs/{organization}/servers/{server}/sites/{site}/deployments/deploy-hook' => 'updateDeploymentTriggerUrl',
        'POST /orgs/{organization}/servers/{server}/sites/{site}/deployments/push-to-deploy' => 'enablePushToDeploy',
        'DELETE /orgs/{organization}/servers/{server}/sites/{site}/deployments/push-to-deploy' => 'disablePushToDeploy',
        // Recipes
        'POST /orgs/{organization}/recipes/{recipe}/runs' => 'createRecipeRun',
        'GET /orgs/{organization}/recipes/{recipe}/runs' => 'recipeRuns',
        'GET /orgs/{organization}/recipes/{recipe}/runs/{log}' => 'recipeRun',
        'POST /orgs/{organization}/teams/{team}/recipes' => 'createTeamRecipesShare',
        'DELETE /orgs/{organization}/teams/{team}/recipes/{recipe}' => 'deleteTeamRecipesShare',
        // SSH Keys - 'key' endpoints for server's main SSH key
        'GET /orgs/{organization}/servers/{server}/key' => 'serverKey',
        'PUT /orgs/{organization}/servers/{server}/key' => 'updateServerKey',
        // Scheduled Jobs
        'GET /orgs/{organization}/servers/{server}/scheduled-jobs/{job}/output' => 'scheduledJobOutput',
        'GET /orgs/{organization}/servers/{server}/sites/{site}/scheduled-jobs/{job}/output' => 'siteScheduledJobOutput',
        'POST /orgs/{organization}/servers/{server}/sites/{site}/scheduled-jobs' => 'createSiteScheduledJob',
        'GET /orgs/{organization}/servers/{server}/sites/{site}/scheduled-jobs' => 'siteScheduledJobs',
        'GET /orgs/{organization}/servers/{server}/sites/{site}/scheduled-jobs/{job}' => 'siteScheduledJob',
        'DELETE /orgs/{organization}/servers/{server}/sites/{site}/scheduled-jobs/{job}' => 'deleteSiteScheduledJob',
        // Server Credentials - team sharing
        'POST /orgs/{organization}/teams/{team}/server-credentials' => 'createTeamServerCredentialsShare',
        'DELETE /orgs/{organization}/teams/{team}/server-credentials/{credential}' => 'deleteTeamServerCredentialsShare',
        // Servers - team sharing and special endpoints
        'GET /orgs/{organization}/servers/{server}/events/{event}/output' => 'serverEventOutput',
        'GET /orgs/{organization}/servers/{server}/php/versions/{phpVersion}/configs/fpm' => 'phpFpm',
        'PUT /orgs/{organization}/servers/{server}/php/versions/{phpVersion}/configs/fpm' => 'updatePhpFpm',
        'GET /orgs/{organization}/servers/{server}/php/versions/{phpVersion}/configs/cli' => 'phpCli',
        'PUT /orgs/{organization}/servers/{server}/php/versions/{phpVersion}/configs/cli' => 'updatePhpCli',
        'GET /orgs/{organization}/servers/{server}/php/versions/{phpVersion}/configs/pool' => 'phpPool',
        'PUT /orgs/{organization}/servers/{server}/php/versions/{phpVersion}/configs/pool' => 'updatePhpPool',
        'POST /orgs/{organization}/servers/{server}/php/versions' => 'installPhpVersion',
        'POST /orgs/{organization}/teams/{team}/servers' => 'createTeamServersShare',
        'DELETE /orgs/{organization}/teams/{team}/servers/{server}' => 'deleteTeamServersShare',
        // Sites - special nested resources and configurations
        'GET /orgs/{organization}/servers/{server}/sites/{site}/commands/{command}/output' => 'commandOutput',
        'GET /orgs/{organization}/servers/{server}/sites/{site}/logs/nginx-access' => 'siteNginxAccessLog',
        'DELETE /orgs/{organization}/servers/{server}/sites/{site}/logs/nginx-access' => 'deleteSiteNginxAccessLog',
        'GET /orgs/{organization}/servers/{server}/sites/{site}/logs/nginx-error' => 'siteNginxErrorLog',
        'DELETE /orgs/{organization}/servers/{server}/sites/{site}/logs/nginx-error' => 'deleteSiteNginxErrorLog',
        'GET /orgs/{organization}/servers/{server}/sites/{site}/logs/application' => 'siteApplicationLog',
        'DELETE /orgs/{organization}/servers/{server}/sites/{site}/logs/application' => 'deleteSiteApplicationLog',
        'GET /orgs/{organization}/servers/{server}/sites/{site}/domains/{domainRecord}/configurations' => 'domainConfigurations',
        'GET /orgs/{organization}/servers/{server}/sites/{site}/domains/{domainRecord}/nginx' => 'domainNginxConfig',
        'PUT /orgs/{organization}/servers/{server}/sites/{site}/domains/{domainRecord}/nginx' => 'updateDomainNginxConfig',
        'GET /orgs/{organization}/servers/{server}/sites/{site}/domains/{domainRecord}/certificate' => 'domainCertificate',
        'POST /orgs/{organization}/servers/{server}/sites/{site}/domains/{domainRecord}/certificate' => 'createDomainCertificate',
        'DELETE /orgs/{organization}/servers/{server}/sites/{site}/domains/{domainRecord}/certificate' => 'deleteDomainCertificate',
        'GET /orgs/{organization}/servers/{server}/sites/{site}/nginx' => 'siteNginx',
        'PUT /orgs/{organization}/servers/{server}/sites/{site}/nginx' => 'updateSiteNginx',
        'GET /orgs/{organization}/servers/{server}/sites/{site}/environment' => 'siteEnvironment',
        'PUT /orgs/{organization}/servers/{server}/sites/{site}/environment' => 'updateSiteEnvironment',
        'GET /orgs/{organization}/servers/{server}/sites/{site}/healthcheck' => 'siteHealthcheck',
        'PUT /orgs/{organization}/servers/{server}/sites/{site}/healthcheck' => 'updateSiteHealthcheck',
        'GET /orgs/{organization}/servers/{server}/sites/{site}/composer/credentials' => 'composerCredentials',
        'POST /orgs/{organization}/servers/{server}/sites/{site}/composer/credentials' => 'createComposerCredential',
        'GET /orgs/{organization}/servers/{server}/sites/{site}/composer/credentials/{repository}' => 'composerCredential',
        'PUT /orgs/{organization}/servers/{server}/sites/{site}/composer/credentials/{repository}' => 'updateComposerCredential',
        'DELETE /orgs/{organization}/servers/{server}/sites/{site}/composer/credentials/{repository}' => 'deleteComposerCredential',
        'GET /orgs/{organization}/servers/{server}/sites/{site}/npm/credentials' => 'npmCredentials',
        'POST /orgs/{organization}/servers/{server}/sites/{site}/npm/credentials' => 'createNpmCredential',
        'GET /orgs/{organization}/servers/{server}/sites/{site}/npm/credentials/{registry}' => 'npmCredential',
        'PUT /orgs/{organization}/servers/{server}/sites/{site}/npm/credentials/{registry}' => 'updateNpmCredential',
        'DELETE /orgs/{organization}/servers/{server}/sites/{site}/npm/credentials/{registry}' => 'deleteNpmCredential',
        'GET /orgs/{organization}/servers/{server}/sites/{site}/load-balancing-nodes' => 'loadBalancingNodes',
        'PUT /orgs/{organization}/servers/{server}/sites/{site}/load-balancing-nodes' => 'updateLoadBalancingNodes',
        'GET /orgs/{organization}/servers/{server}/sites/{site}/redirect-rules' => 'redirectRules',
        'POST /orgs/{organization}/servers/{server}/sites/{site}/redirect-rules' => 'createRedirectRule',
        'GET /orgs/{organization}/servers/{server}/sites/{site}/redirect-rules/{redirectRule}' => 'redirectRule',
        'DELETE /orgs/{organization}/servers/{server}/sites/{site}/redirect-rules/{redirectRule}' => 'deleteRedirectRule',
        'GET /orgs/{organization}/servers/{server}/sites/{site}/security-rules' => 'securityRules',
        'POST /orgs/{organization}/servers/{server}/sites/{site}/security-rules' => 'createSecurityRule',
        'GET /orgs/{organization}/servers/{server}/sites/{site}/security-rules/{securityRule}' => 'securityRule',
        'PUT /orgs/{organization}/servers/{server}/sites/{site}/security-rules/{securityRule}' => 'updateSecurityRule',
        'DELETE /orgs/{organization}/servers/{server}/sites/{site}/security-rules/{securityRule}' => 'deleteSecurityRule',
        // Teams - member and invitation management
        'GET /orgs/{organization}/teams/{team}/members' => 'teamMembers',
        'GET /orgs/{organization}/teams/{team}/members/{user}' => 'teamMember',
        'DELETE /orgs/{organization}/teams/{team}/members/{user}' => 'deleteTeamMember',
        'PUT /orgs/{organization}/teams/{team}/members/{user}' => 'updateTeamMember',
        'POST /orgs/{organization}/teams/{team}/invites' => 'createTeamInvitation',
        'GET /orgs/{organization}/teams/{team}/invites' => 'teamInvitations',
        'GET /orgs/{organization}/teams/{team}/invites/{invitation}' => 'teamInvitation',
        'DELETE /orgs/{organization}/teams/{team}/invites/{invitation}' => 'deleteTeamInvitation',
    ];

    /**
     * Map an endpoint to its corresponding SDK method name.
     */
    public static function mapEndpointToMethod(string $httpMethod, string $path): ?string
    {
        $httpMethod = strtoupper($httpMethod);
        $path = ltrim($path, '/');

        // Check special cases first (null value means explicitly skip this endpoint)
        $key = "$httpMethod /$path";
        if (array_key_exists($key, static::$specialCases)) {
            return static::$specialCases[$key];
        }

        // Use conventions to infer method name
        return MethodNameConventions::mapToMethodName($httpMethod, $path);
    }

    /**
     * Create a complete endpoint to method mapping for all endpoints.
     */
    public static function createMapping(array $endpoints): array
    {
        $mapping = [];

        foreach ($endpoints as $endpoint) {
            $method = $endpoint['method'];
            $path = $endpoint['path'];
            $signature = "$method $path";

            $sdkMethod = static::mapEndpointToMethod($method, $path);
            if ($sdkMethod) {
                $mapping[$signature] = $sdkMethod;
            }
        }

        return $mapping;
    }

    /**
     * Find SDK method for a given endpoint.
     */
    public static function findMethodForEndpoint(array $endpoint, array $sdkMethods): ?array
    {
        $expectedMethod = static::mapEndpointToMethod($endpoint['method'], $endpoint['path']);

        if (! $expectedMethod) {
            return null;
        }

        return $sdkMethods[$expectedMethod] ?? null;
    }
}
