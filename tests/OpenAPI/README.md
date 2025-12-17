# OpenAPI Compliance Tests

This directory contains dynamic, category-based tests that validate the Forge SDK against the official OpenAPI specification.

## Overview

The testing system is designed to:

1. **Always download the latest OpenAPI spec** - No cached or hardcoded data
2. **Dynamically map endpoints to SDK methods** - No hardcoded endpoint mappings
3. **Verify complete coverage** - Ensures all API endpoints have corresponding SDK methods and tests
4. **Organize by category** - Tests are broken out by API category (Organizations, Servers, Sites, etc.)

## Structure

### Core Classes

- **`OpenAPITestCase`** - Base test class that downloads and parses the OpenAPI spec
- **`OpenAPIEndpointMapper`** - Utility that dynamically maps API endpoints to SDK method names using conventions

### Test Classes

Each API category has its own test class (e.g., `OrganizationsTest`, `ServersTest`, `SitesTest`). Each test class includes three tests:

1. **`test_all_{category}_endpoints_have_sdk_methods()`** - Verifies every API endpoint in the category has a corresponding SDK method
2. **`test_all_{category}_sdk_methods_have_tests()`** - Verifies every SDK method has a test in `ForgeSDKTest.php`
3. **`test_{category}_endpoint_coverage_statistics()`** - Displays coverage statistics for the category

## How It Works

### 1. Spec Download

On test setup, the system downloads the latest OpenAPI spec from:
```
https://forge.laravel.com/api/docs.openapi
```

The spec is cached locally at `forge-openapi.json` (gitignored) for debugging purposes.

### 2. Dynamic Endpoint Mapping

The `OpenAPIEndpointMapper` uses naming conventions to automatically map endpoints to methods:

**Examples:**
- `GET /orgs/{organization}/servers` → `servers()`
- `POST /orgs/{organization}/servers` → `createServer()`
- `DELETE /orgs/{organization}/servers/{server}` → `deleteServer()`
- `PUT /orgs/{organization}/servers/{server}` → `updateServer()`

Special cases (like `GET /orgs` → `organizations()`) are handled explicitly.

### 3. Method & Test Discovery

The system uses reflection to:
- Extract all public methods from the `Forge` class and its action traits
- Parse `ForgeSDKTest.php` to identify which methods have tests
- Compare the two to identify gaps

## Running the Tests

### Run All Categories

```bash
./vendor/bin/phpunit tests/OpenAPI/
```

### Run a Specific Category

```bash
./vendor/bin/phpunit tests/OpenAPI/OrganizationsTest.php
./vendor/bin/phpunit tests/OpenAPI/ServersTest.php
./vendor/bin/phpunit tests/OpenAPI/SitesTest.php
```

### View Coverage Statistics

```bash
./vendor/bin/phpunit tests/OpenAPI/OrganizationsTest.php --testdox
```

Output example:
```
=== Organizations API Coverage ===
Total Endpoints: 7
Mapped to SDK: 7 (100%)
With Tests: 7 (100%)
==================================
```

## Adding New Categories

New test classes are automatically generated when you run:

```bash
php tests/OpenAPI/generate-tests.php
```

This script:
1. Downloads the latest OpenAPI spec
2. Extracts all tags/categories
3. Generates a test file for each category

## Maintaining the Mapper

When the API introduces new endpoints that don't follow standard conventions, you may need to update `OpenAPIEndpointMapper`:

### Add to Special Cases

Edit the `handleSpecialCases()` method in `OpenAPIEndpointMapper.php`:

```php
$specialCases = [
    'GET /special/endpoint' => 'specialMethod',
    // Add more as needed
];
```

### Add to Irregulars

Edit the `singularize()` method for plural/singular mapping:

```php
$irregulars = [
    'databases' => 'database',
    'newPlural' => 'newSingular',
];
```

## Benefits Over Old Approach

### Before (OpenAPIComplianceTest.php)
- ❌ 630+ lines of hardcoded endpoint mappings
- ❌ Required manual updates for each new endpoint
- ❌ Single massive test file
- ❌ Difficult to identify which category has gaps

### After (This System)
- ✅ Zero hardcoded mappings (except special cases)
- ✅ Automatically discovers new endpoints
- ✅ Organized by category (22 focused test files)
- ✅ Clear reporting per category
- ✅ Always uses latest OpenAPI spec

## Coverage Report

Run all tests to see a comprehensive coverage report:

```bash
./vendor/bin/phpunit tests/OpenAPI/ --testdox
```

Each category will display its own statistics, making it easy to identify gaps.
