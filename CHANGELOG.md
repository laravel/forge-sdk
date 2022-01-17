# Release Notes

## [Unreleased](https://github.com/laravel/forge-sdk/compare/v3.11.0...3.x)


## [v3.11.0 (2022-01-17)](https://github.com/laravel/forge-sdk/compare/v3.10.0...v3.11.0)

### Fixed
- Update site aliases ([#135](https://github.com/laravel/forge-sdk/pull/135))


## [v3.10.0 (2021-11-23)](https://github.com/laravel/forge-sdk/compare/v3.9.0...v3.10.0)

### Added
- Add deployment history ([#131](https://github.com/laravel/forge-sdk/pull/131))


## [v3.9.0 (2021-10-05)](https://github.com/laravel/forge-sdk/compare/v3.8.0...v3.9.0)

### Added
- Add support for site and server tags in respective resources ([#126](https://github.com/laravel/forge-sdk/pull/126))
- Add PHP 8 as an installable service ([#127](https://github.com/laravel/forge-sdk/pull/127))


## [v3.8.0 (2021-07-26)](https://github.com/laravel/forge-sdk/compare/v3.7.0...v3.8.0)

### Added
- `executeSiteCommand` now returns an instance of `SiteCommand::class` ([#121](https://github.com/laravel/forge-sdk/pull/121))
- `id` property to the `Event::class` resource ([#121](https://github.com/laravel/forge-sdk/pull/121))


## [v3.7.0 (2021-06-01)](https://github.com/laravel/forge-sdk/compare/v3.6.0...v3.7.0)

### Added
- Add ability to update backup configurations ([#120](https://github.com/laravel/forge-sdk/pull/120))


## [v3.6.0 (2021-04-27)](https://github.com/laravel/forge-sdk/compare/v3.5.0...v3.6.0)

### Added
- Add Site commands to SDK ([#115](https://github.com/laravel/forge-sdk/pull/115))
- Adds new/missing properties to Resources/Site ([#118](https://github.com/laravel/forge-sdk/pull/118))
- Add change site PHP version methods to SDK ([#117](https://github.com/laravel/forge-sdk/pull/117))


## [v3.5.0 (2021-03-02)](https://github.com/laravel/forge-sdk/compare/v3.4.0...v3.5.0)

### Added
- Added meilisearch to serverTypes ([#110](https://github.com/laravel/forge-sdk/pull/110))

### Fixed
- Allow TimeoutException to be passed `null` values ([#111](https://github.com/laravel/forge-sdk/pull/111))


## [v3.4.0 (2021-03-02)](https://github.com/laravel/forge-sdk/compare/v3.3.1...v3.4.0)

### Added
- Add types to Server object ([#106](https://github.com/laravel/forge-sdk/pull/106))
- Nginx Templates ([#107](https://github.com/laravel/forge-sdk/pull/107))


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
