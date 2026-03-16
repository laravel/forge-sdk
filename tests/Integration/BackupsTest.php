<?php

declare(strict_types=1);

namespace Tests\Integration;

use Laravel\Forge\Resources\BackupConfiguration;

class BackupsTest extends IntegrationTestCase
{
    public function test_list_backup_configurations(): void
    {
        $configs = $this->forge()->backupConfigurations($this->organization(), $this->serverId());

        $this->assertIsArray($configs);

        if (count($configs) === 0) {
            // Listing works — no configurations to inspect, but endpoint is functional.
            return;
        }

        $config = $configs[0];
        $this->assertInstanceOf(BackupConfiguration::class, $config);
        $this->assertIsInt($config->id);

        $this->assertTrue(
            is_null($config->name) || is_string($config->name),
            'name should be null or string'
        );
        $this->assertTrue(
            is_null($config->provider) || is_string($config->provider),
            'provider should be null or string'
        );
        $this->assertTrue(
            is_null($config->status) || is_string($config->status),
            'status should be null or string'
        );
        $this->assertTrue(
            is_null($config->directory) || is_string($config->directory),
            'directory should be null or string'
        );
        $this->assertIsArray($config->schedule, 'schedule should be an array');
    }

    public function test_get_single_backup_configuration(): void
    {
        $configs = $this->forge()->backupConfigurations($this->organization(), $this->serverId());

        if (count($configs) === 0) {
            $this->markTestSkipped('No backup configurations found on the test server (requires storage provider + database).');
        }

        $config = $this->forge()->backupConfiguration($this->organization(), $this->serverId(), $configs[0]->id);

        $this->assertInstanceOf(BackupConfiguration::class, $config);
        $this->assertSame($configs[0]->id, $config->id);
    }

    public function test_backup_configuration_has_no_jsonapi_envelope_keys(): void
    {
        $configs = $this->forge()->backupConfigurations($this->organization(), $this->serverId());

        if (count($configs) === 0) {
            $this->markTestSkipped('No backup configurations found on the test server (requires storage provider + database).');
        }

        $config = $configs[0];
        $this->assertArrayNotHasKey('relationships', $config->attributes);
        $this->assertArrayNotHasKey('links', $config->attributes);
    }

    public function test_list_backups_for_configuration(): void
    {
        $configs = $this->forge()->backupConfigurations($this->organization(), $this->serverId());

        if (count($configs) === 0) {
            $this->markTestSkipped('No backup configurations found on the test server (requires storage provider + database).');
        }

        $backups = $this->forge()->backups($this->organization(), $this->serverId(), $configs[0]->id);

        $this->assertIsArray($backups);
    }
}
