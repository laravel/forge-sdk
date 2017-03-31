# Forge PHP SDK (Unofficial)

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
$forge = new Themsaid\Forge\Forge($TOKEN_HERE);
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