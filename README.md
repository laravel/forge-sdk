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

**Version 4.0 uses the Forge API v2 and introduces significant breaking changes.** All endpoints now require an organization slug as the first parameter.

When upgrading from v3.x (API v1), carefully review the [upgrade guide](UPGRADE-4.0.md) for detailed migration instructions.

### Basic Usage

You can create an instance of the SDK like so:

```php
$forge = new Laravel\Forge\Forge($apiToken);
```

#### Getting Your Organization Slug

All API v2 endpoints require an organization slug:

```php
// Get all organizations you have access to
$organizations = $forge->organizations();

// Use the organization slug
$organizationSlug = $organizations[0]->slug;
```

Using the `Forge` instance you may perform multiple actions and retrieve resources:

```php
// Get servers for your organization
$servers = $forge->servers($organizationSlug);

// Get a specific server
$server = $forge->server($organizationSlug, $serverId);

// Create a new server
$server = $forge->createServer($organizationSlug, [
    "provider" => ServerProviders::DIGITAL_OCEAN,
    "credential_id" => 1,
    "name" => "my-server",
    "type" => "app",
    "size" => "01",
    "database_type" => InstallableServices::POSTGRES,
    "php_version" => InstallableServices::PHP_85,
    "region" => "ams2"
]);
```

#### Working with Paginated Results

Collection methods like `servers()`, `sites()`, and `recipes()` return a `Laravel\Forge\CursorPaginator`. You can iterate the current page directly, walk every page lazily, or snapshot a page as a plain array:

```php
// Iterate the current page (foreach hides pagination details)
foreach ($forge->servers($organizationSlug) as $server) {
    echo $server->name;
}

// Lazily iterate across all pages — fetches the next cursor on demand
foreach ($forge->servers($organizationSlug)->lazy() as $server) {
    echo $server->name;
}

// Snapshot the current page as a plain array (also: json_encode($paginator))
$page = $forge->servers($organizationSlug)->toArray();
```

Each resource is represented by an instance like `Laravel\Forge\Resources\Server`, with public properties such as `$name`, `$id`, `$size`, `$region`, and others.

#### Waiting for Async Operations

Some methods wait for the action to complete on Forge's end by periodically checking the resource status:

```php
// This will wait until the site is fully installed (max 30 seconds)
$site = $forge->createSite($organizationSlug, $serverId, [
    'domain' => 'example.com',
    'type' => 'php',
]);
```

You can disable waiting or customize the timeout:

```php
// Don't wait
$site = $forge->createSite($organizationSlug, $serverId, $data, false);

// Wait up to 2 minutes
$site = $forge->setTimeout(120)->createSite($organizationSlug, $serverId, $data);
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
$organization = $forge->organization($organizationSlug);

// Get server credentials for an organization
$credentials = $forge->serverCredentials($organizationSlug);
$credential = $forge->serverCredential($organizationSlug, $credentialId);
```

### Managing Servers

```php
// List servers in an organization
$servers = $forge->servers($organizationSlug);

// Get a specific server
$server = $forge->server($organizationSlug, $serverId);

// Create a new server
$server = $forge->createServer($organizationSlug, $data);

// Delete a server
$forge->deleteServer($organizationSlug, $serverId);

// Server actions
$forge->createServerAction($organizationSlug, $serverId, ['action' => 'reboot']);

// Archived servers
$archivedServers = $forge->archivedServers($organizationSlug);
```

### Managing Sites

```php
// List all sites
$allSites = $forge->sites();

// List sites for an organization
$orgSites = $forge->organizationSites($organizationSlug);

// List sites for a server
$sites = $forge->serverSites($organizationSlug, $serverId);

// Get a specific site
$site = $forge->organizationSite($organizationSlug, $siteId);

// Create a site
$site = $forge->createSite($organizationSlug, $serverId, $data);

// Update a site
$forge->updateSite($organizationSlug, $serverId, $siteId, $data);

// Delete a site
$forge->deleteSite($organizationSlug, $serverId, $siteId);
```

### Site Domains & Certificates

```php
// Manage domains
$domains = $forge->domains($organizationSlug, $serverId, $siteId);
$domain = $forge->createDomain($organizationSlug, $serverId, $siteId, $data);
$forge->updateDomain($organizationSlug, $serverId, $siteId, $domainId, $data);
$forge->deleteDomain($organizationSlug, $serverId, $siteId, $domainId);

// Domain certificates
$certs = $forge->domainCertificates($organizationSlug, $serverId, $siteId, $domainId);
$active = $forge->activeDomainCertificate($organizationSlug, $serverId, $siteId, $domainId);
$cert = $forge->certificate($organizationSlug, $serverId, $siteId, $domainId, $certificateId);
$forge->createCertificate($organizationSlug, $serverId, $siteId, $domainId, $data);
$forge->deleteCertificate($organizationSlug, $serverId, $siteId, $domainId, $certificateId);
```

### Site Deployments

```php
// Deployment webhooks
$webhooks = $forge->webhooks($organizationSlug, $serverId, $siteId);
$forge->createWebhook($organizationSlug, $serverId, $siteId, $data);

// Deployment script
$script = $forge->deploymentScript($organizationSlug, $serverId, $siteId);
$forge->updateDeploymentScript($organizationSlug, $serverId, $siteId, $content);

// Deploy a site
$deployment = $forge->createDeployment($organizationSlug, $serverId, $siteId);

// Deployment status
$status = $forge->deploymentStatus($organizationSlug, $serverId, $siteId);
$forge->updateDeploymentState($organizationSlug, $serverId, $siteId);

// Push to deploy
$forge->createPushToDeploy($organizationSlug, $serverId, $siteId, $data);
$forge->deletePushToDeploy($organizationSlug, $serverId, $siteId);
```

### Laravel Integrations

```php
// Horizon
$horizon = $forge->getHorizon($organizationSlug, $serverId, $siteId);
$forge->createHorizon($organizationSlug, $serverId, $siteId, $data);
$forge->deleteHorizon($organizationSlug, $serverId, $siteId);

// Octane
$octane = $forge->getOctane($organizationSlug, $serverId, $siteId);
$forge->createOctane($organizationSlug, $serverId, $siteId, $data);
$forge->deleteOctane($organizationSlug, $serverId, $siteId);

// Reverb
$reverb = $forge->getReverb($organizationSlug, $serverId, $siteId);
$forge->createReverb($organizationSlug, $serverId, $siteId, $data);
$forge->deleteReverb($organizationSlug, $serverId, $siteId);

// Pulse
$pulse = $forge->getPulse($organizationSlug, $serverId, $siteId);
$forge->createPulse($organizationSlug, $serverId, $siteId, $data);
$forge->deletePulse($organizationSlug, $serverId, $siteId);

// Inertia
$inertia = $forge->getInertia($organizationSlug, $serverId, $siteId);
$forge->createInertia($organizationSlug, $serverId, $siteId, $data);

// Laravel Maintenance
$maintenance = $forge->getMaintenance($organizationSlug, $serverId, $siteId);
$forge->createMaintenance($organizationSlug, $serverId, $siteId, $data);
$forge->deleteMaintenance($organizationSlug, $serverId, $siteId);

// Laravel Scheduler
$scheduler = $forge->getScheduler($organizationSlug, $serverId, $siteId);
$forge->createScheduler($organizationSlug, $serverId, $siteId, $data);
$forge->deleteScheduler($organizationSlug, $serverId, $siteId);
```

### Site Workers

```php
$workers = $forge->workers($organizationSlug, $serverId, $siteId);
$worker = $forge->worker($organizationSlug, $serverId, $siteId, $workerId);
$worker = $forge->createWorker($organizationSlug, $serverId, $siteId, $data);
$forge->deleteWorker($organizationSlug, $serverId, $siteId, $workerId);

// Worker actions
$forge->createWorkerAction($organizationSlug, $serverId, $siteId, $workerId, ['action' => 'restart']);
```

### Site Configuration

```php
// Environment file
$env = $forge->siteEnvironment($organizationSlug, $serverId, $siteId);
$forge->updateSiteEnvironment($organizationSlug, $serverId, $siteId, $content);

// Nginx configuration
$nginx = $forge->siteNginx($organizationSlug, $serverId, $siteId);
$forge->updateSiteNginx($organizationSlug, $serverId, $siteId, $content);

// PHP version
$phpVersion = $forge->sitePhp($organizationSlug, $serverId, $siteId);
$forge->updateSitePhp($organizationSlug, $serverId, $siteId, ['version' => 'php84']);
```

### Site Commands

```php
$commands = $forge->commands($organizationSlug, $serverId, $siteId);
$command = $forge->command($organizationSlug, $serverId, $siteId, $commandId);
$forge->createCommand($organizationSlug, $serverId, $siteId, [
    'command' => 'php artisan migrate'
]);
$output = $forge->commandOutput($organizationSlug, $serverId, $siteId, $commandId);
```

### Site Logs

```php
// Nginx access log
$log = $forge->siteNginxAccessLog($organizationSlug, $serverId, $siteId);
$forge->deleteSiteNginxAccessLog($organizationSlug, $serverId, $siteId);

// Nginx error log
$log = $forge->siteNginxErrorLog($organizationSlug, $serverId, $siteId);
$forge->deleteSiteNginxErrorLog($organizationSlug, $serverId, $siteId);

// Application log
$log = $forge->siteApplicationLog($organizationSlug, $serverId, $siteId);
$forge->deleteSiteApplicationLog($organizationSlug, $serverId, $siteId);
```

### Site Heartbeats

```php
$heartbeats = $forge->heartbeats($organizationSlug, $serverId, $siteId);
$heartbeat = $forge->heartbeat($organizationSlug, $serverId, $siteId, $heartbeatId);
$heartbeat = $forge->createHeartbeat($organizationSlug, $serverId, $siteId, $data);
$forge->updateHeartbeat($organizationSlug, $serverId, $siteId, $heartbeatId, $data);
$forge->deleteHeartbeat($organizationSlug, $serverId, $siteId, $heartbeatId);
```

### Managing Databases

```php
// Database schemas
$databases = $forge->databases($organizationSlug, $serverId);
$database = $forge->database($organizationSlug, $serverId, $databaseId);
$database = $forge->createDatabase($organizationSlug, $serverId, $data, $wait = true);
$forge->deleteDatabase($organizationSlug, $serverId, $databaseId);
$forge->syncDatabases($organizationSlug, $serverId);

// Database users
$users = $forge->databaseUsers($organizationSlug, $serverId);
$user = $forge->databaseUser($organizationSlug, $serverId, $userId);
$user = $forge->createDatabaseUser($organizationSlug, $serverId, $data, $wait = true);
$forge->updateDatabaseUser($organizationSlug, $serverId, $userId, $data);
$forge->deleteDatabaseUser($organizationSlug, $serverId, $userId);

// Database password
$forge->updateDatabasePassword($organizationSlug, $serverId, ['password' => 'new-password']);
```

### Background Processes (formerly Daemons)

```php
$processes = $forge->backgroundProcesses($organizationSlug, $serverId);
$process = $forge->backgroundProcess($organizationSlug, $serverId, $processId);
$process = $forge->createBackgroundProcess($organizationSlug, $serverId, $data);
$forge->updateBackgroundProcess($organizationSlug, $serverId, $processId, $data);
$forge->deleteBackgroundProcess($organizationSlug, $serverId, $processId);

// Process log
$log = $forge->backgroundProcessLog($organizationSlug, $serverId, $processId);
```

### Scheduled Jobs

```php
$jobs = $forge->scheduledJobs($organizationSlug, $serverId);
$job = $forge->scheduledJob($organizationSlug, $serverId, $jobId);
$job = $forge->createScheduledJob($organizationSlug, $serverId, $data);
$forge->deleteScheduledJob($organizationSlug, $serverId, $jobId);

// Job output
$output = $forge->scheduledJobOutput($organizationSlug, $serverId, $jobId);
```

### Server Events

```php
$events = $forge->serverEvents($organizationSlug, $serverId);
$event = $forge->serverEvent($organizationSlug, $serverId, $eventId);
$output = $forge->serverEventOutput($organizationSlug, $serverId, $eventId);
```

### PHP Version Management

```php
// List installed PHP versions
$versions = $forge->phpVersions($organizationSlug, $serverId);

// Install a new PHP version
$forge->installPhpVersion($organizationSlug, $serverId, ['version' => 'php84']);

// Get/Update/Delete PHP version
$version = $forge->phpVersion($organizationSlug, $serverId, $phpVersion);
$forge->updatePhpVersion($organizationSlug, $serverId, $phpVersion, $data);
$forge->deletePhpVersion($organizationSlug, $serverId, $phpVersion);

// PHP configs
$fpmConfig = $forge->phpFpmConfig($organizationSlug, $serverId, $phpVersion);
$forge->updatePhpFpmConfig($organizationSlug, $serverId, $phpVersion, $content);

$cliConfig = $forge->phpCliConfig($organizationSlug, $serverId, $phpVersion);
$forge->updatePhpCliConfig($organizationSlug, $serverId, $phpVersion, $content);

$poolConfig = $forge->phpPoolConfig($organizationSlug, $serverId, $phpVersion);
$forge->updatePhpPoolConfig($organizationSlug, $serverId, $phpVersion, $content);

// PHP CLI/Site versions
$cliVersion = $forge->phpCliVersion($organizationSlug, $serverId);
$forge->updatePhpCliVersion($organizationSlug, $serverId, ['version' => 'php84']);

$siteVersion = $forge->phpSiteVersion($organizationSlug, $serverId);
$forge->updatePhpSiteVersion($organizationSlug, $serverId, ['version' => 'php84']);

// PHP settings
$maxUploadSize = $forge->phpMaxUploadSize($organizationSlug, $serverId);
$forge->updatePhpMaxUploadSize($organizationSlug, $serverId, ['size' => '256M']);

$maxExecutionTime = $forge->phpMaxExecutionTime($organizationSlug, $serverId);
$forge->updatePhpMaxExecutionTime($organizationSlug, $serverId, ['time' => '60']);

// OPcache
$opcache = $forge->phpOpcache($organizationSlug, $serverId);
$forge->createPhpOpcache($organizationSlug, $serverId, $data);
$forge->deletePhpOpcache($organizationSlug, $serverId);
```

### Server Service Actions

```php
// Nginx
$forge->performNginxAction($organizationSlug, $serverId, ['action' => 'restart']);

// MySQL
$forge->performMySQLAction($organizationSlug, $serverId, ['action' => 'restart']);

// PostgreSQL
$forge->performPostgresAction($organizationSlug, $serverId, ['action' => 'restart']);

// Redis
$forge->performRedisAction($organizationSlug, $serverId, ['action' => 'restart']);

// PHP
$forge->performPHPAction($organizationSlug, $serverId, ['action' => 'restart']);

// Supervisor
$forge->performSupervisorAction($organizationSlug, $serverId, ['action' => 'restart']);
```

### Server SSH Keys

```php
$keys = $forge->sshKeys($organizationSlug, $serverId);
$key = $forge->sshKey($organizationSlug, $serverId, $keyId);
$forge->createSshKey($organizationSlug, $serverId, $data);
$forge->deleteSshKey($organizationSlug, $serverId, $keyId);

// Server's public key
$publicKey = $forge->serverPublicKey($organizationSlug, $serverId);
$forge->updateServerPublicKey($organizationSlug, $serverId, $data);
```

### Firewall Rules

```php
$rules = $forge->firewallRules($organizationSlug, $serverId);
$rule = $forge->firewallRule($organizationSlug, $serverId, $ruleId);
$forge->createFirewallRule($organizationSlug, $serverId, $data);
$forge->deleteFirewallRule($organizationSlug, $serverId, $ruleId);
```

### Server Monitors

```php
$monitors = $forge->monitors($organizationSlug, $serverId);
$monitor = $forge->monitor($organizationSlug, $serverId, $monitorId);
$monitor = $forge->createMonitor($organizationSlug, $serverId, $data);
$forge->deleteMonitor($organizationSlug, $serverId, $monitorId);
```

### Server Logs

The log key is a hyphenated service slug. Common keys include `nginx-access`, `nginx-error`,
`redis-server`, and `unattended-upgrades`. PHP logs use the `php-{version}` format, such as
`php-8.4`. The keys available for a given server depend on the services installed on it.

```php
$log = $forge->serverLog($organizationSlug, $serverId, 'nginx-error');
$forge->deleteServerLog($organizationSlug, $serverId, 'nginx-error');
```

### Nginx Templates

```php
$templates = $forge->nginxTemplates($organizationSlug, $serverId);
$template = $forge->nginxTemplate($organizationSlug, $serverId, $templateId);
$template = $forge->createNginxTemplate($organizationSlug, $serverId, $data);
$forge->updateNginxTemplate($organizationSlug, $serverId, $templateId, $data);
$forge->deleteNginxTemplate($organizationSlug, $serverId, $templateId);
```

### Security Rules

```php
$rules = $forge->securityRules($organizationSlug, $serverId, $siteId);
$rule = $forge->securityRule($organizationSlug, $serverId, $siteId, $ruleId);
$rule = $forge->createSecurityRule($organizationSlug, $serverId, $siteId, $data);
$forge->updateSecurityRule($organizationSlug, $serverId, $siteId, $ruleId, $data);
$forge->deleteSecurityRule($organizationSlug, $serverId, $siteId, $ruleId);
```

### Redirect Rules

```php
$rules = $forge->redirectRules($organizationSlug, $serverId, $siteId);
$rule = $forge->redirectRule($organizationSlug, $serverId, $siteId, $ruleId);
$forge->createRedirectRule($organizationSlug, $serverId, $siteId, $data);
$forge->deleteRedirectRule($organizationSlug, $serverId, $siteId, $ruleId);
```

### Managing Recipes

```php
// Organization recipes
$recipes = $forge->recipes($organizationSlug);
$recipe = $forge->recipe($organizationSlug, $recipeId);
$recipe = $forge->createRecipe($organizationSlug, $data);
$forge->updateRecipe($organizationSlug, $recipeId, $data);
$forge->deleteRecipe($organizationSlug, $recipeId);

// Recipe runs
$runs = $forge->recipeRuns($organizationSlug, $recipeId);
$run = $forge->recipeRun($organizationSlug, $recipeId, $logId);
$forge->createRecipeRun($organizationSlug, $recipeId, $data);

// Forge-provided recipes
$forgeRecipes = $forge->forgeRecipes();
$forgeRecipe = $forge->forgeRecipe($forgeRecipeId);
$forge->createForgeRecipeRun($forgeRecipeId, $data);

// Team recipes
$teamRecipes = $forge->teamRecipes($organizationSlug, $teamId);
$forge->shareRecipeWithTeam($organizationSlug, $teamId, $data);
$forge->deleteRecipeShare($organizationSlug, $teamId, $recipeId);
```

### Teams

```php
$teams = $forge->teams($organizationSlug);
$team = $forge->team($organizationSlug, $teamId);
$team = $forge->createTeam($organizationSlug, $data);
$forge->updateTeam($organizationSlug, $teamId, $data);
$forge->deleteTeam($organizationSlug, $teamId);

// Team members
$members = $forge->teamMembers($organizationSlug, $teamId);
$member = $forge->teamMember($organizationSlug, $teamId, $userId);
$forge->updateTeamMember($organizationSlug, $teamId, $userId, $data);
$forge->deleteTeamMember($organizationSlug, $teamId, $userId);

// Team invitations
$invitations = $forge->teamInvitations($organizationSlug, $teamId);
$invitation = $forge->teamInvitation($organizationSlug, $teamId, $invitationId);
$invitation = $forge->createTeamInvitation($organizationSlug, $teamId, $data);
$forge->deleteTeamInvitation($organizationSlug, $teamId, $invitationId);

// Team server shares
$servers = $forge->teamServers($organizationSlug, $teamId);
$forge->createTeamServerShare($organizationSlug, $teamId, $data);
$forge->deleteTeamServerShare($organizationSlug, $teamId, $serverId);

// Team credentials
$credentials = $forge->teamServerCredentials($organizationSlug, $teamId);
$forge->shareServerCredential($organizationSlug, $teamId, $data);
$forge->deleteServerCredentialShare($organizationSlug, $teamId, $credentialId);
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
$roles = $forge->roles($organizationSlug);
$role = $forge->role($organizationSlug, $roleId);
$role = $forge->createRole($organizationSlug, $data);
$forge->updateRole($organizationSlug, $roleId, $data);
$forge->deleteRole($organizationSlug, $roleId);

// Role permissions
$permissions = $forge->rolePermissions($organizationSlug, $roleId);
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
