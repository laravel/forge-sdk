# Laravel Forge SDK

<a href="https://github.com/laravel/forge-sdk/actions"><img src="https://github.com/laravel/forge-sdk/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/forge-sdk"><img src="https://img.shields.io/packagist/dt/laravel/forge-sdk" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/forge-sdk"><img src="https://img.shields.io/packagist/v/laravel/forge-sdk" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/forge-sdk"><img src="https://img.shields.io/packagist/l/laravel/forge-sdk" alt="License"></a>

## Introduction

The [Laravel Forge](https://forge.laravel.com) SDK provides an expressive interface for interacting with Forge's API and managing Laravel Forge servers.

## Official Documentation

### Installation

To install the SDK in your project you need to require the package via composer:

```bash
composer require laravel/forge-sdk
```

### Upgrading

When upgrading to a new major version of Forge SDK, it's important that you carefully review [the upgrade guide](https://github.com/laravel/forge-sdk/blob/master/UPGRADE.md).

### Basic Usage

You can create an instance of the SDK like so:

```php
$forge = new Laravel\Forge\Forge(TOKEN_HERE);
```

Using the `Forge` instance you may perform multiple actions as well as retrieve the different resources Forge's API provides:

```php
$servers = $forge->servers();
```

This will give you an array of servers that you have access to, where each server is represented by an instance of `Laravel\Forge\Resources\Server`, this instance has multiple public properties like `$name`, `$id`, `$size`, `$region`, and others.

You may also retrieve a single server using:

```php
$server = $forge->server(SERVER_ID_HERE);
```

On multiple actions supported by this SDK you may need to pass some parameters, for example when creating a new server:

```php
$server = $forge->createServer([
    "provider"=> ServerProviders::DIGITAL_OCEAN,
    "credential_id"=> 1,
    "name"=> "test-via-api",
    "type"=> ServerTypes::APP,
    "size"=> "01",
    "database"=> "test123",
    "database_type" => InstallableServices::POSTGRES,
    "php_version"=> InstallableServices::PHP_71,
    "region"=> "ams2"
]);
```

These parameters will be used in the POST request sent to Forge servers, you can find more information about the parameters needed for each action on
[Forge's official API documentation](https://forge.laravel.com/api-documentation).

Notice that this request for example will only start the server creation process, your server might need a few minutes before it completes provisioning, you'll need to check the server's `$isReady` property to know if it's ready or not yet.

Some SDK methods however waits for the action to complete on Forge's end, we do this by periodically contacting Forge servers and checking if our action has completed, for example:

```php
$forge->createSite(SERVER_ID, [SITE_PARAMETERS]);
```

This method will ping Forge servers every 5 seconds and see if the newly created Site's status is `installed` and only return when it's so, in case the waiting exceeded 30 seconds a `Laravel\Forge\Exceptions\TimeoutException` will be thrown.

You can easily stop this behaviour be setting the `$wait` argument to false:

```php
$forge->createSite(SERVER_ID, [SITE_PARAMETERS], false);
```

You can also set the desired timeout value:

```php
$forge->setTimeout(120)->createSite(SERVER_ID, [SITE_PARAMETERS]);
```

### Authenticated User

```php
$forge->user();
```

### Managing Servers

```php
$forge->servers();
$forge->server($serverId);
$forge->createServer(array $data);
$forge->updateServer($serverId, array $data);
$forge->deleteServer($serverId);
$forge->rebootServer($serverId);

// Server access
$forge->revokeAccessToServer($serverId);
$forge->reconnectToServer($serverId);
$forge->reactivateToServer($serverId);
```

On a `Server` instance you may also call:

```php
$server->update(array $data);
$server->delete();
$server->reboot();
$server->revokeAccess();
$server->reconnect();
$server->reactivate();
$server->rebootMysql();
$server->stopMysql();
$server->rebootPostgres();
$server->stopPostgres();
$server->rebootNginx();
$server->stopNginx();
$server->installBlackfire(array $data);
$server->removeBlackfire();
$server->installPapertrail(array $data);
$server->removePapertrail();
$server->enableOPCache();
$server->disableOPCache();
$server->phpVersions();
$server->installPHP($version);
$server->updatePHP($version);
```

### Server SSH Keys

```php
$forge->keys($serverId);
$forge->sshKey($serverId, $keyId);
$forge->createSSHKey($serverId, array $data, $wait = true);
$forge->deleteSSHKey($serverId, $keyId);
```

On a `SSHKey` instance you may also call:

```php
$sshKey->delete();
```

### Server Scheduled Jobs

```php
$forge->jobs($serverId);
$forge->job($serverId, $jobId);
$forge->createJob($serverId, array $data, $wait = true);
$forge->deleteJob($serverId, $jobId);
```

On a `Job` instance you may also call:

```php
$job->delete();
```

### Server Events

```php
$forge->events();
$forge->events($serverId);
```

### Managing Services

```php
// MySQL
$forge->rebootMysql($serverId);
$forge->stopMysql($serverId);

// Postgres
$forge->rebootPostgres($serverId);
$forge->stopPostgres($serverId);

// Nginx
$forge->rebootNginx($serverId);
$forge->stopNginx($serverId);
$forge->siteNginxFile($serverId, $siteId);
$forge->updateSiteNginxFile($serverId, $siteId, $content);

// Blackfire
$forge->installBlackfire($serverId, array $data);
$forge->removeBlackfire($serverId);

// Papertrail
$forge->installPapertrail($serverId, array $data);
$forge->removePapertrail($serverId);

// OPCache
$forge->enableOPCache($serverId);
$forge->disableOPCache($serverId);
```

### Server Daemons

```php
$forge->daemons($serverId);
$forge->daemon($serverId, $daemonId);
$forge->createDaemon($serverId, array $data, $wait = true);
$forge->restartDaemon($serverId, $daemonId, $wait = true);
$forge->deleteDaemon($serverId, $daemonId);
```

On a `Daemon` instance you may also call:

```php
$daemon->restart($wait = true);
$daemon->delete();
```

### Server Firewall Rules

```php
$forge->firewallRules($serverId);
$forge->firewallRule($serverId, $ruleId);
$forge->createFirewallRule($serverId, array $data, $wait = true);
$forge->deleteFirewallRule($serverId, $ruleId);
```

On a `FirewallRule` instance you may also call:

```php
$rule->delete();
```

### Managing Sites

```php
$forge->sites($serverId);
$forge->site($serverId, $siteId);
$forge->createSite($serverId, array $data, $wait = true);
$forge->updateSite($serverId, $siteId, array $data);
$forge->refreshSiteToken($serverId, $siteId);
$forge->deleteSite($serverId, $siteId);

// Add Site Aliases
$forge->addSiteAliases($serverId, $siteId, array $aliases);

// Environment File
$forge->siteEnvironmentFile($serverId, $siteId);
$forge->updateSiteEnvironmentFile($serverId, $siteId, $content);

// Site Repositories and Deployments
$forge->installGitRepositoryOnSite($serverId, $siteId, array $data, $wait = false);
$forge->updateSiteGitRepository($serverId, $siteId, array $data);
$forge->destroySiteGitRepository($serverId, $siteId, $wait = false);
$forge->siteDeploymentScript($serverId, $siteId);
$forge->updateSiteDeploymentScript($serverId, $siteId, $content);
$forge->enableQuickDeploy($serverId, $siteId);
$forge->disableQuickDeploy($serverId, $siteId);
$forge->deploySite($serverId, $siteId, $wait = false);
$forge->resetDeploymentState($serverId, $siteId);
$forge->siteDeploymentLog($serverId, $siteId);
$forge->deploymentHistory($serverId, $siteId);
$forge->deploymentHistoryDeployment($serverId, $siteId, $deploymentId);
$forge->deploymentHistoryOutput($serverId, $siteId, $deploymentId);

// PHP Version
$forge->changeSitePHPVersion($serverId, $siteId, $version);

// Installing Wordpress
$forge->installWordPress($serverId, $siteId, array $data);
$forge->removeWordPress($serverId, $siteId);

// Installing phpMyAdmin
$forge->installPhpMyAdmin($serverId, $siteId, array $data);
$forge->removePhpMyAdmin($serverId, $siteId);

// Updating Node balancing Configuration
$forge->updateNodeBalancingConfiguration($serverId, $siteId, array $data);
```

On a `Site` instance you may also call:

```php
$site->refreshToken();
$site->delete();
$site->installGitRepository(array $data, $wait = false);
$site->updateGitRepository(array $data);
$site->destroyGitRepository($wait = false);
$site->getDeploymentScript();
$site->updateDeploymentScript($content);
$site->enableQuickDeploy();
$site->disableQuickDeploy();
$site->deploySite($wait = false);
$site->resetDeploymentState();
$site->siteDeploymentLog();
$site->getDeploymentHistory();
$site->getDeploymentHistoryDeployment($deploymentId);
$site->getDeploymentHistoryOutput($deploymentId);
$site->installWordPress($data);
$site->removeWordPress();
$site->installPhpMyAdmin($data);
$site->removePhpMyAdmin();
$site->changePHPVersion($version);
$site->siteLog();
```

### Site Workers

```php
$forge->workers($serverId, $siteId);
$forge->worker($serverId, $siteId, $workerId);
$forge->createWorker($serverId, $siteId, array $data, $wait = true);
$forge->deleteWorker($serverId, $siteId, $workerId);
$forge->restartWorker($serverId, $siteId, $workerId, $wait = true);
```

On a `Worker` instance you may also call:

```php
$worker->delete();
$worker->restart($wait = true);
```

### Security Rules

```php
$forge->securityRules($serverId, $siteId);
$forge->securityRule($serverId, $siteId, $ruleId);
$forge->createSecurityRule($serverId, $siteId, array $data);
$forge->deleteSecurityRule($serverId, $siteId, $ruleId);
```

On a `SecurityRule` instance you may also call:

```php
$securityRule->delete();
```

### Site Webhooks

```php
$forge->webhooks($serverId, $siteId);
$forge->webhook($serverId, $siteId, $webhookId);
$forge->createWebhook($serverId, $siteId, array $data);
$forge->deleteWebhook($serverId, $siteId, $webhookId);
```

On a `Webhook` instance you may also call:

```php
$webhook->delete();
```

### Site Commands

```php
$forge->executeSiteCommand($serverId, $siteId, array $data);
$forge->listCommandHistory($serverId, $siteId);
$forge->getSiteCommand($serverId, $siteId, $commandId);
```

### Site SSL Certificates

```php
$forge->certificates($serverId, $siteId);
$forge->certificate($serverId, $siteId, $certificateId);
$forge->createCertificate($serverId, $siteId, array $data, $wait = true);
$forge->deleteCertificate($serverId, $siteId, $certificateId);
$forge->getCertificateSigningRequest($serverId, $siteId, $certificateId);
$forge->installCertificate($serverId, $siteId, $certificateId, array $data, $wait = true);
$forge->activateCertificate($serverId, $siteId, $certificateId, $wait = true);
$forge->obtainLetsEncryptCertificate($serverId, $siteId, $data, $wait = true);
```

On a `Certificate` instance you may also call:

```php
$certificate->delete();
$certificate->getSigningRequest();
$certificate->install($wait = true);
$certificate->activate($wait = true);
```
### Nginx Templates

```php
$forge->nginxTemplates($serverId);
$forge->nginxDefaultTemplate($serverId);
$forge->nginxTemplate($serverId, $templateId);
$forge->createNginxTemplate($serverId, array $data);
$forge->updateNginxTemplate($serverId, $templateId, array $data);
$forge->deleteNginxTemplate($serverId, $templateId);
```

On a `NginxTemplate` instance you may also call:

```php
$nginxTemplate->update(array $data);
$nginxTemplate->delete();
```

### Managing Databases

```php
$forge->databases($serverId);
$forge->database($serverId, $databaseId);
$forge->createDatabase($serverId, array $data, $wait = true);
$forge->updateDatabase($serverId, $databaseId, array $data);
$forge->deleteDatabase($serverId, $databaseId);
$forge->syncDatabases($serverId);

// Users
$forge->databaseUsers($serverId);
$forge->databaseUser($serverId, $userId);
$forge->createDatabaseUser($serverId, array $data, $wait = true);
$forge->updateDatabaseUser($serverId, $userId, array $data);
$forge->deleteDatabaseUser($serverId, $userId);
```

On a `Database` instance you may also call:

```php
$database->update(array $data);
$database->delete();
```

On a `DatabaseUser` instance you may also call:

```php
$databaseUser->update(array $data);
$databaseUser->delete();
```

### Managing Recipes

```php
$forge->recipes();
$forge->recipe($recipeId);
$forge->createRecipe(array $data);
$forge->updateRecipe($recipeId, array $data);
$forge->deleteRecipe($recipeId);
$forge->runRecipe($recipeId, array $data);
```

On a `Recipe` instance you may also call:

```php
$recipe->update(array $data);
$recipe->delete();
$recipe->run(array $data);
```

### Managing Backups

```php
$forge->backupConfigurations($serverId);
$forge->createBackupConfiguration($serverId, array $data);
$forge->updateBackupConfiguration($serverId, $backupConfigurationId, array $data);
$forge->backupConfiguration($serverId, $backupConfigurationId);
$forge->deleteBackupConfiguration($serverId, $backupConfigurationId);
$forge->restoreBackup($serverId, $backupConfigurationId, $backupId);
$forge->deleteBackup($serverId, $backupConfigurationId, $backupId);
```

On a `BackupConfiguration` instance you may also call:

```php
$extendedConfig = $backupConfig->get(); // Load the databases also
$backupConfig->update(array $data);
$backupConfig->delete();
$backupConfig->restoreBackup($backupId);
$backupConfig->deleteBackup($backupId);
```

On a `Backup` instance you may also call:

```php
$backupConfig->delete();
$backupConfig->restore();
```

### Managing Redirects

```php
$forge->redirectRules($serverId, $siteId);
$forge->redirectRule($serverId, $siteId, $ruleId);
$forge->createRedirectRule($serverId, $siteId, array $data, $wait = true);
$forge->deleteRedirectRule($serverId, $siteId, $ruleId);
```

On a `RedirectRule` instance you may also call:

```php
$redirectRule->delete();
```

## Contributing

Thank you for considering contributing to Forge SDK! You can read the contribution guide [here](.github/CONTRIBUTING.md).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

Please review [our security policy](https://github.com/laravel/forge-sdk/security/policy) on how to report security vulnerabilities.

## License

Laravel Forge SDK is open-sourced software licensed under the [MIT license](LICENSE.md).
