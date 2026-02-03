# Laravel Forge SDK

<a href="https://github.com/laravel/forge-sdk/actions"><img src="https://github.com/laravel/forge-sdk/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/forge-sdk"><img src="https://img.shields.io/packagist/dt/laravel/forge-sdk" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/forge-sdk"><img src="https://img.shields.io/packagist/v/laravel/forge-sdk" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/forge-sdk"><img src="https://img.shields.io/packagist/l/laravel/forge-sdk" alt="License"></a>

## Introduction

The [Laravel Forge](https://forge.laravel.com) SDK provides an expressive interface for interacting with Forge's API v2 and managing Laravel Forge servers.

## Official Documentation

### Installation

To install the SDK in your project you need to require the package via composer:

```bash
composer require laravel/forge-sdk
```

### Upgrading from v3.x

**Version 4.0 uses the Forge API v2 and introduces significant breaking changes.** All endpoints now require an organization ID as the first parameter.

When upgrading from v3.x (API v1), carefully review the [upgrade guide](UPGRADE-4.0.md) for detailed migration instructions.

### Basic Usage

You can create an instance of the SDK like so:

```php
$forge = new Laravel\Forge\Forge($apiToken);
```

#### Getting Your Organization ID

All API v2 endpoints require an organization ID:

```php
// Get all organizations you have access to
$organizations = $forge->organizations();

// Use the organization ID
$organizationId = $organizations[0]->id;
```

Using the `Forge` instance you may perform multiple actions and retrieve resources:

```php
$server = $forge->createServer([
    "provider"=> ServerProviders::DIGITAL_OCEAN,
    "credential_id"=> 1,
    "name"=> "test-via-api",
    "type"=> ServerTypes::APP,
    "size"=> "01",
    "database"=> "test123",
    "database_type" => InstallableServices::POSTGRES,
    "php_version"=> InstallableServices::PHP_85,
    "region"=> "ams2"
// Get servers for your organization
$servers = $forge->servers($organizationId);

// Get a specific server
$server = $forge->server($organizationId, $serverId);

// Create a new server
$server = $forge->createServer($organizationId, [
    "provider" => ServerProviders::DIGITAL_OCEAN,
    "credential_id" => 1,
    "name" => "my-server",
    "type" => "app",
    "size" => "01",
    "database_type" => InstallableServices::POSTGRES,
    "php_version"=> InstallableServices::PHP_85,
    "region" => "ams2"
]);
```

Each resource is represented by an instance like `Laravel\Forge\Resources\Server`, with public properties such as `$name`, `$id`, `$size`, `$region`, and others.

#### Waiting for Async Operations

Some methods wait for the action to complete on Forge's end by periodically checking the resource status:

```php
// This will wait until the site is fully installed (max 30 seconds)
$site = $forge->createSite($organizationId, $serverId, [
    'domain' => 'example.com',
    'type' => 'php',
]);
```

You can disable waiting or customize the timeout:

```php
// Don't wait
$site = $forge->createSite($organizationId, $serverId, $data, false);

// Wait up to 2 minutes
$site = $forge->setTimeout(120)->createSite($organizationId, $serverId, $data);
```

If waiting exceeds the timeout, a `Laravel\Forge\Exceptions\TimeoutException` will be thrown.

### Authenticated User

```php
$user = $forge->user();
// or
$user = $forge->me();
```

### Managing Organizations

```php
// Get all organizations
$organizations = $forge->organizations();

// Get a specific organization
$organization = $forge->organization($organizationId);

// Get server credentials for an organization
$credentials = $forge->serverCredentials($organizationId);
$credential = $forge->serverCredential($organizationId, $credentialId);
```

### Managing Servers

```php
// List servers in an organization
$servers = $forge->servers($organizationId);

// Get a specific server
$server = $forge->server($organizationId, $serverId);

// Create a new server
$server = $forge->createServer($organizationId, $data);

// Delete a server
$forge->deleteServer($organizationId, $serverId);

// Server actions
$forge->createServerAction($organizationId, $serverId, ['action' => 'reboot']);

// Archived servers
$archivedServers = $forge->archivedServers($organizationId);
```

### Managing Sites

```php
// List all sites
$allSites = $forge->sites();

// List sites for an organization
$orgSites = $forge->organizationSites($organizationId);

// List sites for a server
$sites = $forge->serverSites($organizationId, $serverId);

// Get a specific site
$site = $forge->organizationSite($organizationId, $siteId);

// Create a site
$site = $forge->createSite($organizationId, $serverId, $data);

// Update a site
$site = $forge->updateSite($organizationId, $serverId, $siteId, $data);

// Delete a site
$forge->deleteSite($organizationId, $serverId, $siteId);
```

### Site Domains & Certificates

```php
// Manage domains
$domains = $forge->domains($organizationId, $serverId, $siteId);
$domain = $forge->createDomain($organizationId, $serverId, $siteId, $data);
$forge->updateDomain($organizationId, $serverId, $siteId, $domainId, $data);
$forge->deleteDomain($organizationId, $serverId, $siteId, $domainId);

// Domain certificates
$cert = $forge->domainCertificate($organizationId, $serverId, $siteId, $domainId);
$forge->createDomainCertificate($organizationId, $serverId, $siteId, $domainId, $data);
$forge->deleteDomainCertificate($organizationId, $serverId, $siteId, $domainId);
```

### Site Deployments

```php
// Deployment webhooks
$webhooks = $forge->webhooks($organizationId, $serverId, $siteId);
$webhook = $forge->createWebhook($organizationId, $serverId, $siteId, $data);

// Deployment script
$script = $forge->deploymentScript($organizationId, $serverId, $siteId);
$forge->updateDeploymentScript($organizationId, $serverId, $siteId, $content);

// Deploy a site
$deployment = $forge->createDeployment($organizationId, $serverId, $siteId);

// Deployment status
$status = $forge->deploymentStatus($organizationId, $serverId, $siteId);
$forge->updateDeploymentState($organizationId, $serverId, $siteId);

// Push to deploy
$forge->createPushToDeploy($organizationId, $serverId, $siteId, $data);
$forge->deletePushToDeploy($organizationId, $serverId, $siteId);
```

### Laravel Integrations

```php
// Horizon
$horizon = $forge->getHorizon($organizationId, $serverId, $siteId);
$forge->createHorizon($organizationId, $serverId, $siteId, $data);
$forge->deleteHorizon($organizationId, $serverId, $siteId);

// Octane
$octane = $forge->getOctane($organizationId, $serverId, $siteId);
$forge->createOctane($organizationId, $serverId, $siteId, $data);
$forge->deleteOctane($organizationId, $serverId, $siteId);

// Reverb
$reverb = $forge->getReverb($organizationId, $serverId, $siteId);
$forge->createReverb($organizationId, $serverId, $siteId, $data);
$forge->deleteReverb($organizationId, $serverId, $siteId);

// Pulse
$pulse = $forge->getPulse($organizationId, $serverId, $siteId);
$forge->createPulse($organizationId, $serverId, $siteId, $data);
$forge->deletePulse($organizationId, $serverId, $siteId);

// Inertia
$inertia = $forge->getInertia($organizationId, $serverId, $siteId);
$forge->createInertia($organizationId, $serverId, $siteId, $data);

// Laravel Maintenance
$maintenance = $forge->getMaintenance($organizationId, $serverId, $siteId);
$forge->createMaintenance($organizationId, $serverId, $siteId, $data);
$forge->deleteMaintenance($organizationId, $serverId, $siteId);

// Laravel Scheduler
$scheduler = $forge->getScheduler($organizationId, $serverId, $siteId);
$forge->createScheduler($organizationId, $serverId, $siteId, $data);
$forge->deleteScheduler($organizationId, $serverId, $siteId);
```

### Site Workers

```php
$workers = $forge->workers($organizationId, $serverId, $siteId);
$worker = $forge->worker($organizationId, $serverId, $siteId, $workerId);
$worker = $forge->createWorker($organizationId, $serverId, $siteId, $data);
$forge->deleteWorker($organizationId, $serverId, $siteId, $workerId);

// Worker actions
$forge->createWorkerAction($organizationId, $serverId, $siteId, $workerId, ['action' => 'restart']);
```

### Site Configuration

```php
// Environment file
$env = $forge->siteEnvironment($organizationId, $serverId, $siteId);
$forge->updateSiteEnvironment($organizationId, $serverId, $siteId, $content);

// Nginx configuration
$nginx = $forge->siteNginx($organizationId, $serverId, $siteId);
$forge->updateSiteNginx($organizationId, $serverId, $siteId, $content);

// PHP version
$phpVersion = $forge->sitePhp($organizationId, $serverId, $siteId);
$forge->updateSitePhp($organizationId, $serverId, $siteId, ['version' => 'php84']);
```

### Site Commands

```php
$commands = $forge->commands($organizationId, $serverId, $siteId);
$command = $forge->command($organizationId, $serverId, $siteId, $commandId);
$command = $forge->createCommand($organizationId, $serverId, $siteId, [
    'command' => 'php artisan migrate'
]);
$output = $forge->commandOutput($organizationId, $serverId, $siteId, $commandId);
```

### Site Logs

```php
// Nginx access log
$log = $forge->siteNginxAccessLog($organizationId, $serverId, $siteId);
$forge->deleteSiteNginxAccessLog($organizationId, $serverId, $siteId);

// Nginx error log
$log = $forge->siteNginxErrorLog($organizationId, $serverId, $siteId);
$forge->deleteSiteNginxErrorLog($organizationId, $serverId, $siteId);

// Application log
$log = $forge->siteApplicationLog($organizationId, $serverId, $siteId);
$forge->deleteSiteApplicationLog($organizationId, $serverId, $siteId);
```

### Site Heartbeats

```php
$heartbeats = $forge->heartbeats($organizationId, $serverId, $siteId);
$heartbeat = $forge->heartbeat($organizationId, $serverId, $siteId, $heartbeatId);
$heartbeat = $forge->createHeartbeat($organizationId, $serverId, $siteId, $data);
$forge->updateHeartbeat($organizationId, $serverId, $siteId, $heartbeatId, $data);
$forge->deleteHeartbeat($organizationId, $serverId, $siteId, $heartbeatId);
```

### Managing Databases

```php
// Database schemas
$databases = $forge->databases($organizationId, $serverId);
$database = $forge->database($organizationId, $serverId, $databaseId);
$database = $forge->createDatabase($organizationId, $serverId, $data, $wait = true);
$forge->deleteDatabase($organizationId, $serverId, $databaseId);
$forge->syncDatabases($organizationId, $serverId);

// Database users
$users = $forge->databaseUsers($organizationId, $serverId);
$user = $forge->databaseUser($organizationId, $serverId, $userId);
$user = $forge->createDatabaseUser($organizationId, $serverId, $data, $wait = true);
$forge->updateDatabaseUser($organizationId, $serverId, $userId, $data);
$forge->deleteDatabaseUser($organizationId, $serverId, $userId);

// Database password
$forge->updateDatabasePassword($organizationId, $serverId, ['password' => 'new-password']);
```

### Background Processes (formerly Daemons)

```php
$processes = $forge->backgroundProcesses($organizationId, $serverId);
$process = $forge->backgroundProcess($organizationId, $serverId, $processId);
$process = $forge->createBackgroundProcess($organizationId, $serverId, $data);
$forge->updateBackgroundProcess($organizationId, $serverId, $processId, $data);
$forge->deleteBackgroundProcess($organizationId, $serverId, $processId);

// Process log
$log = $forge->backgroundProcessLog($organizationId, $serverId, $processId);
```

### Scheduled Jobs

```php
$jobs = $forge->scheduledJobs($organizationId, $serverId);
$job = $forge->scheduledJob($organizationId, $serverId, $jobId);
$job = $forge->createScheduledJob($organizationId, $serverId, $data);
$forge->deleteScheduledJob($organizationId, $serverId, $jobId);

// Job output
$output = $forge->scheduledJobOutput($organizationId, $serverId, $jobId);
```

### Server Events

```php
$events = $forge->serverEvents($organizationId, $serverId);
$event = $forge->serverEvent($organizationId, $serverId, $eventId);
$output = $forge->serverEventOutput($organizationId, $serverId, $eventId);
```

### PHP Version Management

```php
// List installed PHP versions
$versions = $forge->phpVersions($organizationId, $serverId);

// Install a new PHP version
$forge->installPhpVersion($organizationId, $serverId, ['version' => 'php84']);

// Get/Update/Delete PHP version
$version = $forge->phpVersion($organizationId, $serverId, $phpVersion);
$forge->updatePhpVersion($organizationId, $serverId, $phpVersion, $data);
$forge->deletePhpVersion($organizationId, $serverId, $phpVersion);

// PHP configs
$fpmConfig = $forge->phpFpmConfig($organizationId, $serverId, $phpVersion);
$forge->updatePhpFpmConfig($organizationId, $serverId, $phpVersion, $content);

$cliConfig = $forge->phpCliConfig($organizationId, $serverId, $phpVersion);
$forge->updatePhpCliConfig($organizationId, $serverId, $phpVersion, $content);

$poolConfig = $forge->phpPoolConfig($organizationId, $serverId, $phpVersion);
$forge->updatePhpPoolConfig($organizationId, $serverId, $phpVersion, $content);

// PHP CLI/Site versions
$cliVersion = $forge->phpCliVersion($organizationId, $serverId);
$forge->updatePhpCliVersion($organizationId, $serverId, ['version' => 'php84']);

$siteVersion = $forge->phpSiteVersion($organizationId, $serverId);
$forge->updatePhpSiteVersion($organizationId, $serverId, ['version' => 'php84']);

// PHP settings
$maxUploadSize = $forge->phpMaxUploadSize($organizationId, $serverId);
$forge->updatePhpMaxUploadSize($organizationId, $serverId, ['size' => '256M']);

$maxExecutionTime = $forge->phpMaxExecutionTime($organizationId, $serverId);
$forge->updatePhpMaxExecutionTime($organizationId, $serverId, ['time' => '60']);

// OPcache
$opcache = $forge->phpOpcache($organizationId, $serverId);
$forge->createPhpOpcache($organizationId, $serverId, $data);
$forge->deletePhpOpcache($organizationId, $serverId);
```

### Server Service Actions

```php
// Nginx
$forge->performNginxAction($organizationId, $serverId, ['action' => 'restart']);

// MySQL
$forge->performMySQLAction($organizationId, $serverId, ['action' => 'restart']);

// PostgreSQL
$forge->performPostgresAction($organizationId, $serverId, ['action' => 'restart']);

// Redis
$forge->performRedisAction($organizationId, $serverId, ['action' => 'restart']);

// PHP
$forge->performPHPAction($organizationId, $serverId, ['action' => 'restart']);

// Supervisor
$forge->performSupervisorAction($organizationId, $serverId, ['action' => 'restart']);
```

### Server SSH Keys

```php
$keys = $forge->sshKeys($organizationId, $serverId);
$key = $forge->sshKey($organizationId, $serverId, $keyId);
$key = $forge->createSSHKey($organizationId, $serverId, $data);
$forge->deleteSSHKey($organizationId, $serverId, $keyId);

// Server's public key
$publicKey = $forge->serverPublicKey($organizationId, $serverId);
$forge->updateServerPublicKey($organizationId, $serverId, $data);
```

### Firewall Rules

```php
$rules = $forge->firewallRules($organizationId, $serverId);
$rule = $forge->firewallRule($organizationId, $serverId, $ruleId);
$rule = $forge->createFirewallRule($organizationId, $serverId, $data);
$forge->deleteFirewallRule($organizationId, $serverId, $ruleId);
```

### Server Monitors

```php
$monitors = $forge->monitors($organizationId, $serverId);
$monitor = $forge->monitor($organizationId, $serverId, $monitorId);
$monitor = $forge->createMonitor($organizationId, $serverId, $data);
$forge->deleteMonitor($organizationId, $serverId, $monitorId);
```

### Server Logs

```php
$log = $forge->serverLog($organizationId, $serverId, $logKey);
$forge->deleteServerLog($organizationId, $serverId, $logKey);
```

### Nginx Templates

```php
$templates = $forge->nginxTemplates($organizationId, $serverId);
$template = $forge->nginxTemplate($organizationId, $serverId, $templateId);
$template = $forge->createNginxTemplate($organizationId, $serverId, $data);
$forge->updateNginxTemplate($organizationId, $serverId, $templateId, $data);
$forge->deleteNginxTemplate($organizationId, $serverId, $templateId);
```

### Security Rules

```php
$rules = $forge->securityRules($organizationId, $serverId, $siteId);
$rule = $forge->securityRule($organizationId, $serverId, $siteId, $ruleId);
$rule = $forge->createSecurityRule($organizationId, $serverId, $siteId, $data);
$forge->updateSecurityRule($organizationId, $serverId, $siteId, $ruleId, $data);
$forge->deleteSecurityRule($organizationId, $serverId, $siteId, $ruleId);
```

### Redirect Rules

```php
$rules = $forge->redirectRules($organizationId, $serverId, $siteId);
$rule = $forge->redirectRule($organizationId, $serverId, $siteId, $ruleId);
$rule = $forge->createRedirectRule($organizationId, $serverId, $siteId, $data);
$forge->deleteRedirectRule($organizationId, $serverId, $siteId, $ruleId);
```

### Managing Recipes

```php
// Organization recipes
$recipes = $forge->recipes($organizationId);
$recipe = $forge->recipe($organizationId, $recipeId);
$recipe = $forge->createRecipe($organizationId, $data);
$forge->updateRecipe($organizationId, $recipeId, $data);
$forge->deleteRecipe($organizationId, $recipeId);

// Recipe runs
$runs = $forge->recipeRuns($organizationId, $recipeId);
$run = $forge->recipeRun($organizationId, $recipeId, $logId);
$run = $forge->createRecipeRun($organizationId, $recipeId, $data);

// Forge-provided recipes
$forgeRecipes = $forge->forgeRecipes();
$forgeRecipe = $forge->forgeRecipe($forgeRecipeId);
$forge->createForgeRecipeRun($forgeRecipeId, $data);

// Team recipes
$teamRecipes = $forge->teamRecipes($organizationId, $teamId);
$forge->shareRecipeWithTeam($organizationId, $teamId, $data);
$forge->deleteRecipeShare($organizationId, $teamId, $recipeId);
```

### Teams

```php
$teams = $forge->teams($organizationId);
$team = $forge->team($organizationId, $teamId);
$team = $forge->createTeam($organizationId, $data);
$forge->updateTeam($organizationId, $teamId, $data);
$forge->deleteTeam($organizationId, $teamId);

// Team members
$members = $forge->teamMembers($organizationId, $teamId);
$member = $forge->teamMember($organizationId, $teamId, $userId);
$forge->updateTeamMember($organizationId, $teamId, $userId, $data);
$forge->deleteTeamMember($organizationId, $teamId, $userId);

// Team invitations
$invitations = $forge->teamInvitations($organizationId, $teamId);
$invitation = $forge->teamInvitation($organizationId, $teamId, $invitationId);
$invitation = $forge->createTeamInvitation($organizationId, $teamId, $data);
$forge->deleteTeamInvitation($organizationId, $teamId, $invitationId);

// Team server shares
$servers = $forge->teamServers($organizationId, $teamId);
$forge->createTeamServerShare($organizationId, $teamId, $data);
$forge->deleteTeamServerShare($organizationId, $teamId, $serverId);

// Team credentials
$credentials = $forge->teamServerCredentials($organizationId, $teamId);
$forge->shareServerCredential($organizationId, $teamId, $data);
$forge->deleteServerCredentialShare($organizationId, $teamId, $credentialId);
```

### Roles & Permissions

```php
// Predefined roles
$predefinedRoles = $forge->predefinedRoles();
$predefinedRole = $forge->predefinedRole($roleId);

// Permissions
$permissions = $forge->permissions();
$permission = $forge->permission($permissionId);

// Organization roles
$roles = $forge->roles($organizationId);
$role = $forge->role($organizationId, $roleId);
$role = $forge->createRole($organizationId, $data);
$forge->updateRole($organizationId, $roleId, $data);
$forge->deleteRole($organizationId, $roleId);

// Role permissions
$permissions = $forge->rolePermissions($organizationId, $roleId);
```

### Providers

```php
$providers = $forge->providers();
$provider = $forge->provider($providerId);

// Provider sizes
$sizes = $forge->providerSizes($providerId);
$size = $forge->providerSize($providerId, $sizeId);

// Provider regions
$regions = $forge->providerRegions($providerId);
$region = $forge->providerRegion($providerId, $regionId);

// Region sizes
$regionSizes = $forge->providerRegionSizes($providerId, $regionId);
$regionSize = $forge->providerRegionSize($providerId, $regionId, $sizeId);
```

## API Documentation

For detailed information about request parameters and response structures, see the [official Forge API documentation](https://forge.laravel.com/docs/api).

## Contributing

Thank you for considering contributing to Forge SDK! You can read the contribution guide [here](.github/CONTRIBUTING.md).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

Please review [our security policy](https://github.com/laravel/forge-sdk/security/policy) on how to report security vulnerabilities.

## License

Laravel Forge SDK is open-sourced software licensed under the [MIT license](LICENSE.md).
