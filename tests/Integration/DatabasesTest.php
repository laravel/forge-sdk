<?php

declare(strict_types=1);

namespace Tests\Integration;

use Laravel\Forge\Resources\Database;

class DatabasesTest extends IntegrationTestCase
{
    public function test_list_databases(): void
    {
        $databases = $this->forge()->databases($this->organization(), $this->serverId());

        $this->assertIsArray($databases);

        if (count($databases) === 0) {
            $this->markTestSkipped('No databases found on the test server.');
        }

        $db = $databases[0];
        $this->assertInstanceOf(Database::class, $db);
        $this->assertIsInt($db->id);
        $this->assertIsString($db->name);
        $this->assertNotEmpty($db->name);
        $this->assertIsString($db->status);
        $this->assertIsString($db->createdAt, 'createdAt should be hydrated');
    }

    public function test_get_single_database(): void
    {
        $databases = $this->forge()->databases($this->organization(), $this->serverId());

        if (count($databases) === 0) {
            $this->markTestSkipped('No databases found on the test server.');
        }

        $db = $this->forge()->database($this->organization(), $this->serverId(), $databases[0]->id);

        $this->assertInstanceOf(Database::class, $db);
        $this->assertSame($databases[0]->id, $db->id);
        $this->assertIsString($db->name);
        $this->assertIsString($db->status);
    }

    public function test_database_has_no_jsonapi_envelope_keys(): void
    {
        $databases = $this->forge()->databases($this->organization(), $this->serverId());

        if (count($databases) === 0) {
            $this->markTestSkipped('No databases found on the test server.');
        }

        $db = $databases[0];
        $this->assertArrayNotHasKey('relationships', $db->attributes);
        $this->assertArrayNotHasKey('links', $db->attributes);
    }
}
