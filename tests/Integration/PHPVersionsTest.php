<?php

declare(strict_types=1);

namespace Tests\Integration;

use Laravel\Forge\Resources\PHPVersion;

class PHPVersionsTest extends IntegrationTestCase
{
    public function test_list_php_versions(): void
    {
        $versions = $this->forge()->phpVersions($this->organization(), $this->serverId());

        $this->assertIsArray($versions);
        $this->assertNotEmpty($versions, 'Expected at least one PHP version on the test server.');

        $version = $versions[0];
        $this->assertInstanceOf(PHPVersion::class, $version);
        $this->assertIsInt($version->id);
        $this->assertIsString($version->version);
        $this->assertNotEmpty($version->version);
        $this->assertIsString($version->status);

        // Core properties
        $this->assertTrue(
            is_null($version->binaryName) || is_string($version->binaryName),
            'binaryName should be null or string'
        );
        $this->assertTrue(
            is_null($version->displayableVersion) || is_string($version->displayableVersion),
            'displayableVersion should be null or string'
        );
        $this->assertTrue(
            is_null($version->usedAsDefault) || is_bool($version->usedAsDefault),
            'usedAsDefault should be null or bool'
        );
        $this->assertTrue(
            is_null($version->usedOnCli) || is_bool($version->usedOnCli),
            'usedOnCli should be null or bool'
        );

        // v2 timestamps
        $this->assertTrue(
            is_null($version->createdAt) || is_string($version->createdAt),
            'createdAt should be null or string'
        );
        $this->assertTrue(
            is_null($version->updatedAt) || is_string($version->updatedAt),
            'updatedAt should be null or string'
        );
    }

    public function test_php_version_has_no_jsonapi_envelope_keys(): void
    {
        $versions = $this->forge()->phpVersions($this->organization(), $this->serverId());

        $version = $versions[0];
        $this->assertArrayNotHasKey('relationships', $version->attributes);
        $this->assertArrayNotHasKey('links', $version->attributes);
    }
}
