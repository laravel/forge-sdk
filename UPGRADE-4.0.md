# Upgrade Guide: v3.x to v4.0

This guide covers everything you need to know to upgrade from Forge SDK v3.x (API v1) to v4.0 (API v2).

> [!CAUTION]
> v4.0 requires **PHP 8.2** or higher. Support for PHP 7.2 through 8.1 has been dropped.

## Breaking Changes

### Organization-Scoped Endpoints

This is the most significant change in v4.0. Nearly all resource endpoints now require an organization slug as the first parameter.

**v3.x:**
```php
$forge->servers();
$forge->server($serverId);
$forge->createServer($data);
```

**v4.0:**
```php
$forge->servers($organizationSlug);
$forge->server($organizationSlug, $serverId);
$forge->createServer($organizationSlug, $data);
```

This applies to **all** endpoints except:
- `$forge->user()` / `$forge->me()`
- `$forge->organizations()`
- `$forge->sites()` (global, returns all sites across organizations)
- `$forge->providers()` / `$forge->providerSizes()` / `$forge->providerRegions()`
- `$forge->permissions()` / `$forge->predefinedRoles()`
- `$forge->forgeRecipes()`

### Renamed Methods

Methods have been renamed to match API v2 terminology:

| v3.x | v4.0 |
|------|------|
| `daemons()`, `daemon()`, `createDaemon()` | `backgroundProcesses()`, `backgroundProcess()`, `createBackgroundProcess()` |
| `jobs()`, `job()`, `createJob()` | `scheduledJobs()`, `scheduledJob()`, `createScheduledJob()` |
| `sites($serverId)` | `serverSites($organizationSlug, $serverId)` |
| `rebootServer($serverId)` | `createServerAction($organizationSlug, $serverId, ['action' => 'reboot'])` |
| `rebootNginx($serverId)` | `performNginxAction($organizationSlug, $serverId, ['action' => 'restart'])` |
| `rebootMySQL($serverId)` | `performMySQLAction($organizationSlug, $serverId, ['action' => 'restart'])` |
| `installPHP($serverId, $version)` | `installPhpVersion($organizationSlug, $serverId, $data)` |

Service actions now follow a consistent pattern for all services:

```php
$forge->performNginxAction($organizationSlug, $serverId, ['action' => 'restart']);
$forge->performMySQLAction($organizationSlug, $serverId, ['action' => 'restart']);
$forge->performPostgresAction($organizationSlug, $serverId, ['action' => 'restart']);
$forge->performRedisAction($organizationSlug, $serverId, ['action' => 'restart']);
$forge->performPHPAction($organizationSlug, $serverId, ['action' => 'restart']);
$forge->performSupervisorAction($organizationSlug, $serverId, ['action' => 'restart']);
```

### Removed Alias Methods

The following alias methods have been removed. Use the primary method instead:

| Removed | Use Instead |
|---------|-------------|
| `allSites()` | `sites()` |
| `siteNginxConfig()` | `siteNginx()` |
| `updateSiteNginxConfig()` | `updateSiteNginx()` |
| `siteLog()` | `siteNginxAccessLog()`, `siteNginxErrorLog()`, or `siteApplicationLog()` |
| `deleteSiteLog()` | `deleteSiteNginxAccessLog()`, `deleteSiteNginxErrorLog()`, or `deleteSiteApplicationLog()` |

### Renamed Action Traits

If you extend or directly reference action traits, these have been renamed or merged:

| v3.x Trait | v4.0 Trait |
|------------|------------|
| `ManagesCertificates` | *Merged into `ManagesSites`* |
| `ManagesCredentials` | `ManagesServerCredentials` |
| `ManagesDaemons` | `ManagesBackgroundProcesses` |
| `ManagesDatabaseUsers` | *Merged into `ManagesDatabases`* |
| `ManagesJobs` | `ManagesScheduledJobs` |
| `ManagesNginxTemplates` | `ManagesNginx` |
| `ManagesSiteCommands` | `ManagesCommands` |
| `ManagesWebhooks` | *Merged into `ManagesDeployments`* |
| `ManagesWorkers` | *Removed — workers are no longer available in API v2* |

### Removed Resource Convenience Methods

Many Resource convenience methods have been removed because the underlying API methods no longer exist in v4. If you relied on these, use the Forge client methods directly instead.

**Server** — 9 methods removed:
- `$server->update()` — no `updateServer()` in v4
- `$server->revokeAccess()` — no `revokeAccessToServer()` in v4
- `$server->reconnect()` — no `reconnectToServer()` in v4
- `$server->reactivate()` — no `reactivateToServer()` in v4
- `$server->installBlackfire()` / `$server->removeBlackfire()` — Blackfire integration removed
- `$server->installPapertrail()` / `$server->removePapertrail()` — Papertrail integration removed
- `$server->updatePHP()` — v4 `updatePhpVersion()` requires a `$phpVersionId`, cannot be called from the resource

**Site** — 17 methods removed:
- `$site->refreshToken()`, `$site->installGitRepository()`, `$site->updateGitRepository()`, `$site->destroyGitRepository()`, `$site->createDeployKey()`, `$site->destroyDeployKey()` — git operations removed from API v2
- `$site->enableQuickDeploy()`, `$site->resetDeploymentState()`, `$site->siteDeploymentLog()` — no v4 equivalent
- `$site->enableHipchatNotifications()`, `$site->disableHipchatNotifications()` — HipChat integration removed
- `$site->setDeploymentFailureEmails()` — removed
- `$site->installWordPress()`, `$site->removeWordPress()` — WordPress support removed
- `$site->installPhpMyAdmin()`, `$site->removePhpMyAdmin()` — phpMyAdmin support removed
- `$site->changePHPVersion()` — no `changeSitePHPVersion()` in v4

**Certificate** — all 4 convenience methods removed (`delete()`, `getSigningRequest()`, `install()`, `activate()`). Certificates are now managed at the domain level via `domainCertificate()` / `createDomainCertificate()` / `deleteDomainCertificate()`.

**Database** — `$database->update()` removed (no `updateDatabase()` in v4).

### Changed Resource Convenience Method Signatures

Some convenience methods have changed their signatures or target different underlying methods:

**Server:**
- `$server->rebootPHP(array $data)` → `$server->rebootPHP()` — no longer accepts `$data`
- `$server->installPHP(string $version)` → now returns `PHPVersion` instead of `void`

**Site:**
- `$site->updateDeploymentScript(string $content, bool $autoSource)` → `$site->updateDeploymentScript(array $data)` — accepts an array instead of individual params
- `$site->deploySite(bool $wait = true): Site` → `$site->deploySite(): Deployment` — no longer accepts `$wait`, returns `Deployment` instead of `Site`

**Recipe:**
- `$recipe->run(array $data): void` → `$recipe->run(array $data): RecipeRun` — now returns a `RecipeRun` instance

### Removed Resource Properties

**Server:**
- `$blackfireStatus` — Blackfire integration removed
- `$papertrailStatus` — Papertrail integration removed

**Site:**
- `$hipchatRoom`, `$slackChannel`, `$telegramChatId`, `$telegramChatTitle`, `$teamsWebhookUrl`, `$discordWebhookUrl` — notification channel properties removed
- `$balancingStatus` — removed

### Strict Types and Typed Properties

All files now declare `strict_types=1` and use native PHP type declarations for properties, parameters, and return types.

**Typed resource properties:**

All resource properties are now typed with native PHP types. Properties are nullable with a `null` default:

```php
// v3.x
class Server extends Resource
{
    public $id;
    public $name;
    public $isReady;
    public $tags;
}

// v4.0
class Server extends Resource
{
    public ?int $id = null;
    public ?string $name = null;
    public ?bool $isReady = null;
    public array $tags = [];
}
```

**Typed method signatures:**

All SDK methods now have native parameter and return types:

```php
// v3.x
public function servers();
public function server($serverId);
public function createServer(array $data, $wait = true);

// v4.0
public function servers(string $organizationSlug): array;
public function server(string $organizationSlug, int $serverId): Server;
public function createServer(string $organizationSlug, array $data, bool $wait = true): Server;
```

**Removal of `#[\AllowDynamicProperties]`:**

The `Resource` base class no longer uses `#[\AllowDynamicProperties]`. Undeclared fields from the API response are no longer accessible as dynamic properties. Use the `$attributes` array instead:

```php
// v3.x — dynamic property access worked
$server->someUndeclaredField;

// v4.0 — use the attributes array
$server->attributes['some_undeclared_field'];
```

### API Base URI and Response Format

The SDK now targets Forge API v2:

| | v3.x | v4.0 |
|-|------|------|
| **Base URI** | `https://forge.laravel.com/api/v1/` | `https://forge.laravel.com/api/` |
| **Content-Type** | `application/json` | `application/vnd.api+json` |
| **Response wrapper** | `{"server": {...}}` | `{"data": {...}}` |

The SDK handles response unwrapping internally, but if you access raw HTTP responses directly, the structure has changed.

---

## New Features

### New Traits

v4.0 introduces several new action traits:

- `ManagesDeployments` — webhooks, deployment scripts, push-to-deploy
- `ManagesIntegrations` — Horizon, Octane, Reverb, Inertia, Pulse, Maintenance, Scheduler
- `ManagesLogs`
- `ManagesOrganizations`
- `ManagesProviders`
- `ManagesRoles`
- `ManagesStorageProviders`
- `ManagesTeams`
- `ManagesUser`

### Organizations

```php
$organizations = $forge->organizations();
$organization = $forge->organization($organizationSlug);
```

### Teams, Roles & Permissions

```php
$teams = $forge->teams($organizationSlug);
$team = $forge->team($organizationSlug, $teamId);
$forge->createTeam($organizationSlug, $data);
$members = $forge->teamMembers($organizationSlug, $teamId);
$invitations = $forge->teamInvitations($organizationSlug, $teamId);

$roles = $forge->roles($organizationSlug);
$permissions = $forge->permissions();
$predefinedRoles = $forge->predefinedRoles();
```

### Server Credentials & VPCs

```php
$credentials = $forge->serverCredentials($organizationSlug);
$credential = $forge->serverCredential($organizationSlug, $credentialId);

$vpcs = $forge->vpcs($organizationSlug, $credentialId, $region);
$forge->createVpc($organizationSlug, $credentialId, $region, $data);
```

### Storage Providers

```php
$providers = $forge->storageProviders($organizationSlug);
$provider = $forge->storageProvider($organizationSlug, $storageProviderId);
$provider = $forge->createStorageProvider($organizationSlug, $data);
$provider = $forge->updateStorageProvider($organizationSlug, $storageProviderId, $data);
$forge->deleteStorageProvider($organizationSlug, $storageProviderId);
```

### Providers

```php
$providers = $forge->providers();
$provider = $forge->provider($providerId);
$sizes = $forge->providerSizes($providerId);
$regions = $forge->providerRegions($providerId);
```

### Laravel Integrations

Each integration follows the same pattern (`get`, `create`, `delete`):

```php
$forge->getHorizon($organizationSlug, $serverId, $siteId);
$forge->createHorizon($organizationSlug, $serverId, $siteId, $data);
$forge->deleteHorizon($organizationSlug, $serverId, $siteId);
```

Available integrations: Horizon, Octane, Reverb, Pulse, Inertia, Maintenance, Scheduler.

### Site Deployments

```php
$webhooks = $forge->webhooks($organizationSlug, $serverId, $siteId);
$forge->createWebhook($organizationSlug, $serverId, $siteId, $data);

$script = $forge->deploymentScript($organizationSlug, $serverId, $siteId);
$forge->updateDeploymentScript($organizationSlug, $serverId, $siteId, $data);

$url = $forge->deploymentTriggerUrl($organizationSlug, $serverId, $siteId);
```

### Site Domains & Certificates

```php
$domains = $forge->domains($organizationSlug, $serverId, $siteId);
$domain = $forge->createDomain($organizationSlug, $serverId, $siteId, $data);

$cert = $forge->domainCertificate($organizationSlug, $serverId, $siteId, $domainId);
$forge->createDomainCertificate($organizationSlug, $serverId, $siteId, $domainId, $data);
```

### Site Heartbeats

```php
$heartbeats = $forge->heartbeats($organizationSlug, $serverId, $siteId);
$heartbeat = $forge->createHeartbeat($organizationSlug, $serverId, $siteId, $data);
```

### PHP Version Management

PHP management has been expanded:

```php
$versions = $forge->phpVersions($organizationSlug, $serverId);
$forge->installPhpVersion($organizationSlug, $serverId, ['version' => 'php84']);

// Per-version configuration
$forge->phpFpm($organizationSlug, $serverId, $phpVersionId);
$forge->updatePhpFpm($organizationSlug, $serverId, $phpVersionId, $data);
$forge->phpCli($organizationSlug, $serverId, $phpVersionId);
$forge->phpPool($organizationSlug, $serverId, $phpVersionId);
```

### Forge Recipes

```php
$forgeRecipes = $forge->forgeRecipes();
$forgeRecipe = $forge->forgeRecipe($recipeId);
$forge->createForgeRecipeRun($recipeId, $data);
```

---

## Migration Checklist

### 1. Update PHP to 8.2+

v4.0 requires PHP 8.2 or higher.

### 2. Get Your Organization Slug

```php
$forge = new \Laravel\Forge\Forge($apiKey);
$organizations = $forge->organizations();
$organizationSlug = $organizations[0]->id;
```

### 3. Add Organization Slug to All Method Calls

Add `$organizationSlug` as the first argument to all resource methods. This is the bulk of the migration work.

### 4. Rename Changed Methods

- `daemons()` → `backgroundProcesses()`
- `jobs()` → `scheduledJobs()`
- `allSites()` → `sites()`
- `siteNginxConfig()` → `siteNginx()`
- `updateSiteNginxConfig()` → `updateSiteNginx()`
- Update any direct trait references (see table above)

### 5. Test Thoroughly

The API structure has changed significantly. Test all your integrations carefully.

---

## Compatibility Notes

### Resource Objects

Resource properties are now natively typed. Properties are automatically camelCased from the API response and assigned to typed class properties. Unknown API fields are stored in the `$attributes` array and are no longer accessible as dynamic properties (see "Strict Types and Typed Properties" above).

### Error Handling

Exception handling is unchanged:
- `ValidationException`
- `NotFoundException`
- `ForbiddenException`
- `FailedActionException`
- `RateLimitExceededException`
- `TimeoutException`

### Async Operations

The `$wait` parameter for long-running operations like `createServer()` still works the same way.

---

## Need Help?

1. Check the [API v2 documentation](https://forge.laravel.com/api/docs)
2. Review the [SDK source code](https://github.com/laravel/forge-sdk)
3. [Open an issue](https://github.com/laravel/forge-sdk/issues)
