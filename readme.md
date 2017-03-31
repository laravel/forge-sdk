# Forge PHP SDK

## Installation

To install the SDK in your project you need to require the package via composer:

```
composer require themsaid/forge-sdk
```

Then use Composer's autoload:

```php
require __DIR__.'/../vendor/autoload.php';
```

And finally create an instance of the SDK:

```php
$forge = new \Themsaid\Forge\Forge($TOKEN_HERE);
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

On multiple actions this SDK supports you may need to pass some parameters, for example when creating a new server:

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
the Server's `$isReady` property to know if it's ready or not.

## Managing Servers

```php
// The collection of servers.
$forge->servers();

// Get a server instance.
$forge->server($serverId);

// Create a new server.
$forge->createServer($data);

// Update the given server.
$forge->updateServer($serverId, $data);

// Delete the given server.
$forge->deleteServer($serverId);

// Revoke forge access to the server.
$forge->revokeAccessToServer($serverId);

// Reconnect the server to Forge with a new key.
$forge->reconnectToServer($serverId);

// Reactivate a revoked server.
$forge->reactivateToServer($serverId);

// Reboot MySQL on the server.
$forge->rebootMysql($serverId);

// Stop MySQL on the server.
$forge->stopMysql($serverId);

// Reboot Postgres on the server.
$forge->rebootPostgres($serverId);

// Stop Postgres on the server.
$forge->stopPostgres($serverId);

// Reboot Nginx on the server.
$forge->rebootNginx($serverId);

// Stop Nginx on the server.
$forge->stopNginx($serverId);

// Install Blackfire on the server.
$forge->installBlackfire($serverId, $data);

// Install Papertrail on the server.
$forge->installPapertrail($serverId, $data);

// Remove Papertrail from the server.
$forge->removePapertrail($serverId);
```