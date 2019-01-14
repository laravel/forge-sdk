# Forge PHP SDK

To install the SDK in your project you need to require the package via composer:

```bash
composer require themsaid/forge-sdk
```

Then use Composer's autoload:

```php
require __DIR__.'/../vendor/autoload.php';
```

And finally create an instance of the SDK:

```php
$forge = new Themsaid\Forge\Forge(TOKEN_HERE);
```

## Usage

Using the forge instance you may perform multiple actions as well as retrieve the different resources Forge's API provides:

```php
$servers = $forge->servers();
```

This will give you an array of servers that you have access to, each server is represented by an instance of `Themsaid\Forge\Resources\Server`, this instance has multiple public
properties like `$name`, `$id`, `$size`, `$region`, and others.

You may also retrieve a single server using:

```php
$server = $forge->server(SERVER_ID_HERE);
```

On multiple actions supported by this SDK you may need to pass some parameters, for example when creating a new server:

```php
$server = $forge->createServer([
    "provider"=> "ocean2",
    "credential_id"=> 1,
    "name"=> "test-via-api",
    "size"=> "512MB",
    "database"=> "test123",
    "php_version"=> "php71",
    "region"=> "ams2"
]);
```

These parameters will be used in the POST request sent to Forge servers, you can find more information about the parameters needed for each action on
[Forge's official API documentation](https://forge.laravel.com/api-documentation).

Notice that this request for example will only start the server creation process, your server might need a few minutes before it completes provisioning, you'll need to check
the Server's `$isReady` property to know if it's ready or not yet.

Some SDK methods however waits for the action to complete on Forge's end, we do this by periodically contacting Forge servers and checking if our action has completed, for example:

```php
$forge->createSite(SERVER_ID, [SITE_PARAMETERS]);
```

This method will ping Forge servers every 5 seconds and see if the newly created Site's status is `installed` and only return when it's so, in case the waiting exceeded 30 seconds
a `Themsaid\Forge\Exceptions\TimeoutException` will be thrown.

You can easily stop this behaviour be setting the `$wait` argument to false:

```php
$forge->createSite(SERVER_ID, [SITE_PARAMETERS], false);
```

You can also set the desired timeout value:

```php
$forge->setTimeout(120)->createSite(SERVER_ID, [SITE_PARAMETERS]);
```

## Managing Servers

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

On a Server instance you may also call:

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
$server->upgradePHP();
```

## Server SSH Keys

```php
$forge->keys($serverId);
$forge->SSHKey($serverId, $keyId);
$forge->createSSHKey($serverId, array $data, $wait = true);
$forge->deleteSSHKey($serverId, $keyId);
```

On a SSHKey Instance you may also call:

```php
$sshKey->delete();
```

## Server Scheduled Jobs

```php
$forge->jobs($serverId);
$forge->job($serverId, $jobId);
$forge->createJob($serverId, array $data, $wait = true);
$forge->deleteJob($serverId, $jobId);
```

On a Job Instance you may also call:

```php
$job->delete();
```

## Server Events

```php
$forge->events();
$forge->events($serverId);
```

## Managing Services

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

// PHP
$forge->upgradePHP($serverId);
```

## Server Daemons

```php
$forge->daemons($serverId);
$forge->daemon($serverId, $daemonId);
$forge->createDaemon($serverId, array $data, $wait = true);
$forge->restartDaemon($serverId, $daemonId, $wait = true);
$forge->deleteDaemon($serverId, $daemonId);
```

On a Daemon Instance you may also call:

```php
$daemon->restart($wait = true);
$daemon->delete();
```

## Server Firewall Rules

```php
$forge->firewallRules($serverId);
$forge->firewallRule($serverId, $ruleId);
$forge->createFirewallRule($serverId, array $data, $wait = true);
$forge->deleteFirewallRule($serverId, $ruleId);
```

On a FirewallRule Instance you may also call:

```php
$rule->delete();
```

## Managing Sites

```php
$forge->sites($serverId);
$forge->site($serverId, $siteId);
$forge->createSite($serverId, array $data, $wait = true);
$forge->updateSite($serverId, $siteId, array $data);
$forge->refreshSiteToken($serverId, $siteId);
$forge->deleteSite($serverId, $siteId);

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

// Notifications
$forge->enableHipchatNotifications($serverId, $siteId, array $data);
$forge->disableHipchatNotifications($serverId, $siteId);

// Installing Wordpress
$forge->installWordPress($serverId, $siteId, array $data);
$forge->removeWordPress($serverId, $siteId);

// Updating Node balancing Configuration
$forge->updateNodeBalancingConfiguration($serverId, $siteId, array $data);
```

On a Site Instance you may also call:

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
$site->enableHipchatNotifications(array $data);
$site->disableHipchatNotifications();
$site->installWordPress($data);
$site->removeWordPress();
```


## Site Workers

```php
$forge->workers($serverId, $siteId);
$forge->worker($serverId, $siteId, $workerId);
$forge->createWorker($serverId, $siteId, array $data, $wait = true);
$forge->deleteWorker($serverId, $siteId, $workerId);
$forge->restartWorker($serverId, $siteId, $workerId, $wait = true);
```

On a Worker Instance you may also call:

```php
$worker->delete();
$worker->restart($wait = true);
```

## Site SSL Certificates

```php
$forge->certificates($serverId, $siteId);
$forge->certificate($serverId, $siteId, $certificateId);
$forge->createCertificate($serverId, $siteId, array $data, $wait = true);
$forge->deleteCertificate($serverId, $siteId, $certificateId);
$forge->getCertificateSigningRequest($serverId, $siteId, $certificateId);
$forge->installCertificate($serverId, $siteId, $certificateId, $wait = true);
$forge->activateCertificate($serverId, $siteId, $certificateId, $wait = true);
$forge->obtainLetsEncryptCertificate($serverId, $siteId, $data, $wait = true);
```

On a Certificate Instance you may also call:

```php
$certificate->delete();
$certificate->getSigningRequest();
$certificate->install($wait = true);
$certificate->activate($wait = true);
```

## Managing MySQL

```php
$forge->mysqlDatabases($serverId);
$forge->mysqlDatabase($serverId, $databaseId);
$forge->createMysqlDatabase($serverId, array $data, $wait = true);
$forge->updateMysqlDatabase($serverId, $databaseId, array $data);
$forge->deleteMysqlDatabase($serverId, $databaseId);

// Users
$forge->mysqlUsers($serverId);
$forge->mysqlUser($serverId, $userId);
$forge->createMysqlUser($serverId, array $data, $wait = true);
$forge->updateMysqlUser($serverId, $userId, array $data);
$forge->deleteMysqlUser($serverId, $userId);
```

On a MysqlDatabase Instance you may also call:

```php
$database->update(array $data);
$database->delete();
```

On a MysqlUser Instance you may also call:

```php
$databaseUser->update(array $data);
$databaseUser->delete();
```

## Managing Recipes

```php
$forge->recipes();
$forge->recipe($recipeId);
$forge->createRecipe(array $data);
$forge->updateRecipe($recipeId, array $data);
$forge->deleteRecipe($recipeId);
$forge->runRecipe($recipeId, array $data);
```

On a Recipe Instance you may also call:

```php

$recipe->update(array $data);
$recipe->delete();
$recipe->run(array $data);
```


## Testing

Run the tests with:

``` bash
vendor/bin/phpunit
```
