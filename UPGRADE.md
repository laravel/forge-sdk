# Upgrade Guide

## Upgrading To 3.0 From 2.x

### New Package Name

The package has moved to the Laravel organisation and should now be referenced as `laravel/forge-sdk` in your `composer.json`.

### Namespace Changes

The starting namespace for the package has changed from `ThemSaid\\` to `Laravel\\`.

### Minimum PHP Version

PHP 7.2 is the new minimum version.

### Method Changes

The `upgradePHP` method [has been replaced](https://github.com/laravel/forge-sdk/commit/ef5da6e2c30ffb58674fb2984e8d4a0c31e6ac2c) by the `installPHP` and `updatePHP` methods.

The "Managing MySQL" specific functionality [has been removed](https://github.com/laravel/forge-sdk/pull/86) in favor of the new and general "Managing Databases" functionality.

The `SSHKey` method [has been changed](https://github.com/laravel/forge-sdk/commit/af6860f505fff7a8cff623ab32e3edab73f79559) to `sshKey`.
