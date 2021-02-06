# Release Notes

## [Unreleased](https://github.com/laravel/forge-sdk/compare/v3.3.1...3.x)


## [v3.3.1 (2020-12-14)](https://github.com/laravel/forge-sdk/compare/v3.3.0...v3.3.1)

### Fixed

- Fix deployments `$wait` ([9d24051](https://github.com/laravel/forge-sdk/commit/9d24051ae1cf5fd28109713b7d7712fcd80e194b))


## [v3.3.0 (2020-11-03)](https://github.com/laravel/forge-sdk/compare/v3.2.0...v3.3.0)

### Added
- PHP 8 Support ([#98](https://github.com/laravel/forge-sdk/pull/98))


## [v3.2.0 (2020-09-22)](https://github.com/laravel/forge-sdk/compare/v3.1.0...v3.2.0)

### Added
- Add site aliases function ([#95](https://github.com/laravel/forge-sdk/pull/95))
- Forge defaults as class constants ([#94](https://github.com/laravel/forge-sdk/pull/94))


## [v3.1.0 (2020-09-01)](https://github.com/laravel/forge-sdk/compare/v3.0.0...v3.1.0)

### Added
- Add restart worker ([d62ecb4](https://github.com/laravel/forge-sdk/commit/d62ecb4b654b0fa5db1dc5e8cb0131bb1ef92d27))

### Fixed
- Fix delete action for RedirectRule ([#91](https://github.com/laravel/forge-sdk/pull/91))
- Fix Worker resource delete method ([#90](https://github.com/laravel/forge-sdk/pull/90))


## [v3.0.0 (2020-08-25)](https://github.com/laravel/forge-sdk/compare/v2.2...v3.0.0)

### Added
- Security rules ([#83](https://github.com/laravel/forge-sdk/pull/83))
- Add installPHP and updatePHP methods ([ef5da6e](https://github.com/laravel/forge-sdk/commit/ef5da6e2c30ffb58674fb2984e8d4a0c31e6ac2c))
- Add full PHP management methods ([#84](https://github.com/laravel/forge-sdk/pull/84))
- Adds installPhpMyAdmin install and deletePhpMyAdmin methods ([#85](https://github.com/laravel/forge-sdk/pull/85))
- Adds `resetDeploymentState()` and `siteDeploymentLog()` to `Site` resource ([#88](https://github.com/laravel/forge-sdk/pull/88))

### Changed
- Renamed namespaces ([fdcc996](https://github.com/laravel/forge-sdk/commit/fdcc996209681e252ddc060ee983fec327af10de))
- Changed `$apiKey` property visibility ([88cbb08](https://github.com/laravel/forge-sdk/commit/88cbb08014b3ea3768e47c3a9e14367b7d10f59f))
- Update rebootPHP method ([f619c0f](https://github.com/laravel/forge-sdk/commit/f619c0f57dbd3b632b5e424f2288135f811719a1))
- Remove references to only MySQL databases ([#86](https://github.com/laravel/forge-sdk/pull/86), [628b083](https://github.com/laravel/forge-sdk/commit/628b08303a3801e9279ea2b561e7d899327992bb))
- Rename `SSHKey` method to `sshKey` ([af6860f](https://github.com/laravel/forge-sdk/commit/af6860f505fff7a8cff623ab32e3edab73f79559))

### Fixed
- Fix empty collection ([26dedb8](https://github.com/laravel/forge-sdk/commit/26dedb8ca7dfac49d0f6fe35d3444eb3d0a52a7b))


## v2.2 (2020-05-14)
