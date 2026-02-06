# Upgrade Guide: v3.x to v4.0

This document outlines all breaking changes when upgrading from Forge SDK v3.x (API v1) to v4.0 (API v2).

## Major Breaking Changes

[!CAUTION]
PHP Support has been dropped for versions below PHP 8.2.

### 1. API Version Change

The SDK now uses the Forge API v2, which has a completely different URL structure and response format.

**v3.x:**
```php
Base URI: https://forge.laravel.com/api/v1/
```

**v4.0:**
```php
Base URI: https://forge.laravel.com/api/
```

### 2. Organization-Scoped Endpoints

**BREAKING:** All resource endpoints now require an organization ID as the first parameter.

**v3.x:**
```php
$forge->servers();
$forge->server($serverId);
$forge->createServer($data);
```

**v4.0:**
```php
$forge->servers($organizationId);
$forge->server($organizationId, $serverId);
$forge->createServer($organizationId, $data);
```

This affects **ALL** endpoints except:
- `$forge->user()` / `$forge->me()`
- `$forge->organizations()`
- `$forge->providers()`
- `$forge->permissions()`
- `$forge->predefinedRoles()`

### 3. Response Format Change (JSON:API)

**BREAKING:** API responses now follow the JSON:API specification.

**v3.x:**
```php
// Response: {"server": {...}}
$server = $forge->server($id);
```

**v4.0:**
```php
// Response: {"data": {...}}
$server = $forge->server($orgId, $serverId);
```

The SDK handles this internally, but if you're accessing raw responses, they will be different.

### 4. Content-Type Headers

**v3.x:**
```
Accept: application/json
Content-Type: application/json
```

**v4.0:**
```
Accept: application/vnd.api+json
Content-Type: application/vnd.api+json
```

### 5. Renamed Action Traits

If you're extending or directly using action traits, these have been renamed:

| v3.x Trait | v4.0 Trait |
|------------|------------|
| `ManagesBackups` | *Removed* (not in API v2) |
| `ManagesCertificates` | *Merged into ManagesSites* |
| `ManagesCredentials` | `ManagesServerCredentials` |
| `ManagesDaemons` | `ManagesBackgroundProcesses` |
| `ManagesDatabaseUsers` | *Merged into ManagesDatabases* |
| `ManagesJobs` | `ManagesScheduledJobs` |
| `ManagesNginxTemplates` | `ManagesNginx` |
| `ManagesSiteCommands` | `ManagesCommands` |
| `ManagesWebhooks` | *Merged into ManagesDeployments* |
| `ManagesWorkers` | *Merged into ManagesSites* |

**New in v4.0:**
- `ManagesIntegrations` (Horizon, Octane, Reverb, Inertia, Pulse, Maintenance, Scheduler)
- `ManagesDeployments` (webhooks, deployment scripts, push-to-deploy)
- `ManagesOrganizations`
- `ManagesProviders`
- `ManagesRoles`
- `ManagesTeams`
- `ManagesUser`
- `ManagesLogs`

### 6. Method Signature Changes

#### Servers

**v3.x:**
```php
$servers = $forge->servers();
$server = $forge->server($serverId);
$server = $forge->createServer($data);
$forge->updateServer($serverId, $data);
$forge->deleteServer($serverId);
$forge->rebootServer($serverId);
```

**v4.0:**
```php
$servers = $forge->servers($organizationId);
$server = $forge->server($organizationId, $serverId);
$server = $forge->createServer($organizationId, $data);
$server->update($data); // Or $forge->updateServer($orgId, $serverId, $data)
$server->delete(); // Or $forge->deleteServer($orgId, $serverId)
$forge->createServerAction($organizationId, $serverId, ['action' => 'reboot']);
```

#### Sites

**v3.x:**
```php
$sites = $forge->sites($serverId);
$site = $forge->site($serverId, $siteId);
$site = $forge->createSite($serverId, $data);
```

**v4.0:**
```php
$sites = $forge->serverSites($organizationId, $serverId);
$site = $forge->organizationSite($organizationId, $siteId);
$site = $forge->createSite($organizationId, $serverId, $data);
```

#### Databases

**v3.x:**
```php
$databases = $forge->databases($serverId);
$database = $forge->database($serverId, $databaseId);
$database = $forge->createDatabase($serverId, $data);
```

**v4.0:**
```php
$databases = $forge->databases($organizationId, $serverId);
$database = $forge->database($organizationId, $serverId, $databaseId);
$database = $forge->createDatabase($organizationId, $serverId, $data);
```

#### Database Users

**v3.x:**
```php
$users = $forge->databaseUsers($serverId);
```

**v4.0:**
```php
$users = $forge->databaseUsers($organizationId, $serverId);
```

#### Background Processes (formerly Daemons)

**v3.x:**
```php
$daemons = $forge->daemons($serverId);
$daemon = $forge->daemon($serverId, $daemonId);
$daemon = $forge->createDaemon($serverId, $data);
```

**v4.0:**
```php
$processes = $forge->backgroundProcesses($organizationId, $serverId);
$process = $forge->backgroundProcess($organizationId, $serverId, $processId);
$process = $forge->createBackgroundProcess($organizationId, $serverId, $data);
```

#### Scheduled Jobs (formerly Jobs)

**v3.x:**
```php
$jobs = $forge->jobs($serverId);
$job = $forge->job($serverId, $jobId);
```

**v4.0:**
```php
$jobs = $forge->scheduledJobs($organizationId, $serverId);
$job = $forge->scheduledJob($organizationId, $serverId, $jobId);
```

#### Firewall Rules

**v3.x:**
```php
$rules = $forge->firewallRules($serverId);
```

**v4.0:**
```php
$rules = $forge->firewallRules($organizationId, $serverId);
```

#### Nginx Templates

**v3.x:**
```php
$templates = $forge->nginxTemplates($serverId);
```

**v4.0:**
```php
$templates = $forge->nginxTemplates($organizationId, $serverId);
```

#### Recipes

**v3.x:**
```php
$recipes = $forge->recipes();
$recipe = $forge->recipe($recipeId);
```

**v4.0:**
```php
$recipes = $forge->recipes($organizationId);
$recipe = $forge->recipe($organizationId, $recipeId);

// New: Forge-provided recipes
$forgeRecipes = $forge->forgeRecipes();
```

### 7. Removed Features

The following features from v3.x are not available in API v2:

- **Backups** (`ManagesBackups` trait)
  - `backupConfigurations()`
  - `backupConfiguration()`
  - `createBackupConfiguration()`
  - `updateBackupConfiguration()`
  - `deleteBackupConfiguration()`
  - `restoreBackup()`
  - `deleteBackup()`

If you rely on these features, you must stay on v3.x or use the Forge UI.

### 8. New Features in v4.0

#### Organizations

```php
$organizations = $forge->organizations();
$organization = $forge->organization($organizationId);
```

#### Server Credentials

```php
$credentials = $forge->serverCredentials($organizationId);
$credential = $forge->serverCredential($organizationId, $credentialId);

// VPC support
$vpcs = $forge->vpcs($organizationId, $credentialId, $region);
$forge->createVpc($organizationId, $credentialId, $region, $data);
```

#### Providers

```php
$providers = $forge->providers();
$provider = $forge->provider($providerId);
$sizes = $forge->providerSizes($providerId);
$regions = $forge->providerRegions($providerId);
```

#### Teams

```php
$teams = $forge->teams($organizationId);
$team = $forge->team($organizationId, $teamId);
$forge->createTeam($organizationId, $data);

$members = $forge->teamMembers($organizationId, $teamId);
$invitations = $forge->teamInvitations($organizationId, $teamId);
```

#### Roles & Permissions

```php
$roles = $forge->roles($organizationId);
$permissions = $forge->permissions();
$predefinedRoles = $forge->predefinedRoles();
```

#### Laravel Integrations

```php
// Horizon
$forge->getHorizon($organizationId, $serverId, $siteId);
$forge->createHorizon($organizationId, $serverId, $siteId, $data);
$forge->deleteHorizon($organizationId, $serverId, $siteId);

// Octane
$forge->getOctane($organizationId, $serverId, $siteId);
$forge->createOctane($organizationId, $serverId, $siteId, $data);
$forge->deleteOctane($organizationId, $serverId, $siteId);

// Reverb
$forge->getReverb($organizationId, $serverId, $siteId);
$forge->createReverb($organizationId, $serverId, $siteId, $data);
$forge->deleteReverb($organizationId, $serverId, $siteId);

// Pulse
$forge->getPulse($organizationId, $serverId, $siteId);
$forge->createPulse($organizationId, $serverId, $siteId, $data);
$forge->deletePulse($organizationId, $serverId, $siteId);

// And more: Inertia, Maintenance, Scheduler
```

#### Site Deployments

```php
// Webhooks
$webhooks = $forge->webhooks($organizationId, $serverId, $siteId);
$forge->createWebhook($organizationId, $serverId, $siteId, $data);

// Deployment script
$script = $forge->deploymentScript($organizationId, $serverId, $siteId);
$forge->updateDeploymentScript($organizationId, $serverId, $siteId, $data);

// Deploy trigger
$url = $forge->deploymentTriggerUrl($organizationId, $serverId, $siteId);
```

#### Site Domains

```php
$domains = $forge->domains($organizationId, $serverId, $siteId);
$domain = $forge->createDomain($organizationId, $serverId, $siteId, $data);

// Domain certificates
$cert = $forge->domainCertificate($organizationId, $serverId, $siteId, $domainId);
$forge->createDomainCertificate($organizationId, $serverId, $siteId, $domainId, $data);
```

#### Site Workers

```php
$workers = $forge->workers($organizationId, $serverId, $siteId);
$worker = $forge->createWorker($organizationId, $serverId, $siteId, $data);
```

#### Site Heartbeats

```php
$heartbeats = $forge->heartbeats($organizationId, $serverId, $siteId);
$heartbeat = $forge->createHeartbeat($organizationId, $serverId, $siteId, $data);
```

### 9. PHP Version Management

Enhanced PHP version management in v4.0:

**v3.x:**
```php
$forge->installPHP($serverId, $version);
```

**v4.0:**
```php
// List all installed PHP versions
$versions = $forge->phpVersions($organizationId, $serverId);

// Install new version
$forge->installPhpVersion($organizationId, $serverId, ['version' => 'php84']);

// Manage PHP configs
$forge->phpFpmConfig($organizationId, $serverId, $phpVersion);
$forge->updatePhpFpmConfig($organizationId, $serverId, $phpVersion, $content);
$forge->phpCliConfig($organizationId, $serverId, $phpVersion);
$forge->phpPoolConfig($organizationId, $serverId, $phpVersion);
```

### 10. Server Service Actions

**v3.x:**
```php
$forge->rebootNginx($serverId);
$forge->rebootMySQL($serverId);
```

**v4.0:**
```php
$forge->performNginxAction($organizationId, $serverId, ['action' => 'restart']);
$forge->performMySQLAction($organizationId, $serverId, ['action' => 'restart']);
$forge->performPostgresAction($organizationId, $serverId, ['action' => 'restart']);
$forge->performRedisAction($organizationId, $serverId, ['action' => 'restart']);
$forge->performPHPAction($organizationId, $serverId, ['action' => 'restart']);
$forge->performSupervisorAction($organizationId, $serverId, ['action' => 'restart']);
```

## Migration Strategy

### Step 1: Get Your Organization ID

```php
$forge = new \Laravel\Forge\Forge($apiKey);

// Get your organizations
$organizations = $forge->organizations();

// Use the first organization or find the one you need
$organizationId = $organizations[0]->id;
```

### Step 2: Update All Method Calls

Go through your codebase and add the `$organizationId` parameter as the first argument to all resource methods.

### Step 3: Update Renamed Methods

- Replace `daemons()` with `backgroundProcesses()`
- Replace `jobs()` with `scheduledJobs()`
- Update any direct trait usage

### Step 4: Remove Backup-Related Code

If you're using backup features, you'll need to:
- Use the Forge UI for backups
- Stay on v3.x
- Implement your own backup solution

### Step 5: Test Thoroughly

The API structure has changed significantly. Test all your integrations carefully.

## Compatibility Notes

### Resource Objects

Resource objects remain largely compatible, though some properties may have changed names or been added/removed based on the API v2 response structure.

### Error Handling

Exception handling remains the same:
- `ValidationException`
- `NotFoundException`
- `ForbiddenException`
- `FailedActionException`
- `RateLimitExceededException`
- `TimeoutException`

### Async Operations

The `$wait` parameter for async operations like `createServer()` and `createDatabase()` still works the same way.

## Need Help?

If you encounter issues during migration:

1. Check the [API v2 documentation](https://forge.laravel.com/api/docs)
2. Review the [SDK source code](https://github.com/laravel/forge-sdk)
3. [Open an issue](https://github.com/laravel/forge-sdk/issues)

## Version Support

- **v3.x**: Supports Forge API v1 (deprecated but still functional)
- **v4.0+**: Supports Forge API v2 only

We recommend upgrading to v4.0 to ensure compatibility with future Forge features.
