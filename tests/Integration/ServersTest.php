<?php

declare(strict_types=1);

namespace Tests\Integration;

use Laravel\Forge\CursorPaginator;
use Laravel\Forge\Resources\Event;
use Laravel\Forge\Resources\Server;

class ServersTest extends IntegrationTestCase
{
    public function test_list_servers(): void
    {
        $servers = $this->forge()->servers($this->organization());

        $this->assertInstanceOf(CursorPaginator::class, $servers);
        $this->assertNotEmpty($servers, 'Expected at least one server in the organization.');
        $this->assertContainsOnlyInstancesOf(Server::class, $servers);

        $server = $servers[0];
        $this->assertInstanceOf(Server::class, $server);
        $this->assertIsInt($server->id);
        $this->assertIsString($server->name);
        $this->assertNotEmpty($server->name);
    }

    public function test_get_server_core_properties(): void
    {
        $server = $this->forge()->server($this->organization(), $this->serverId());

        $this->assertInstanceOf(Server::class, $server);
        $this->assertSame($this->serverId(), $server->id);
        $this->assertIsString($server->name);
        $this->assertNotEmpty($server->name);
        $this->assertIsBool($server->isReady);
        $this->assertIsArray($server->tags);
        $this->assertIsArray($server->network);
    }

    public function test_get_server_v2_properties(): void
    {
        $server = $this->forge()->server($this->organization(), $this->serverId());

        // These should always be present on a provisioned server
        $this->assertIsString($server->ipAddress, 'ipAddress should be hydrated');
        $this->assertNotEmpty($server->ipAddress);
        $this->assertIsString($server->phpVersion, 'phpVersion should be hydrated');
        $this->assertIsString($server->region, 'region should be hydrated');
        $this->assertIsString($server->size, 'size should be hydrated');
        $this->assertIsString($server->provider, 'provider should be hydrated');
        $this->assertIsString($server->createdAt, 'createdAt should be hydrated');
        $this->assertIsString($server->updatedAt, 'updatedAt should be hydrated');

        // New v2 fields — present on a ready server
        $this->assertIsString($server->ubuntuVersion, 'ubuntuVersion should be hydrated');
        $this->assertIsInt($server->sshPort, 'sshPort should be hydrated');
        $this->assertIsString($server->databaseType, 'databaseType should be hydrated');
        $this->assertIsString($server->connectionStatus, 'connectionStatus should be hydrated');

        // Optional v2 fields — may be null but should have correct types when present
        $this->assertTrue(
            is_null($server->phpCliVersion) || is_string($server->phpCliVersion),
            'phpCliVersion should be null or string'
        );
        $this->assertTrue(
            is_null($server->opcacheStatus) || is_string($server->opcacheStatus),
            'opcacheStatus should be null or string'
        );
        $this->assertTrue(
            is_null($server->dbStatus) || is_string($server->dbStatus),
            'dbStatus should be null or string'
        );
        $this->assertTrue(
            is_null($server->redisStatus) || is_string($server->redisStatus),
            'redisStatus should be null or string'
        );
        $this->assertTrue(
            is_null($server->timezone) || is_string($server->timezone),
            'timezone should be null or string'
        );
        $this->assertTrue(
            is_null($server->localPublicKey) || is_string($server->localPublicKey),
            'localPublicKey should be null or string'
        );
    }

    public function test_server_has_no_jsonapi_envelope_keys(): void
    {
        $server = $this->forge()->server($this->organization(), $this->serverId());

        $this->assertIsArray($server->relationships);
        $this->assertIsArray($server->links);
    }

    public function test_server_events(): void
    {
        $events = $this->forge()->serverEvents($this->organization(), $this->serverId());

        $this->assertInstanceOf(CursorPaginator::class, $events);

        if (count($events) === 0) {
            $this->markTestSkipped('No events found on the test server.');
        }

        $this->assertContainsOnlyInstancesOf(Event::class, $events);

        $event = $events[0];
        $this->assertInstanceOf(Event::class, $event);
        $this->assertIsInt($event->id);
        $this->assertIsString($event->description);
        $this->assertNotEmpty($event->description);
        $this->assertIsString($event->createdAt);
    }

    public function test_get_single_server_event(): void
    {
        $events = $this->forge()->serverEvents($this->organization(), $this->serverId());

        $this->assertInstanceOf(CursorPaginator::class, $events);

        if (count($events) === 0) {
            $this->markTestSkipped('No events found on the test server.');
        }

        $firstEvent = $events[0];
        $event = $this->forge()->serverEvent($this->organization(), $this->serverId(), $firstEvent->id);

        $this->assertInstanceOf(Event::class, $event);
        $this->assertSame($firstEvent->id, $event->id);
        $this->assertIsString($event->description);
        $this->assertNotEmpty($event->description);
        $this->assertIsString($event->createdAt);
    }

    public function test_archived_servers_list(): void
    {
        $servers = $this->forge()->archivedServers($this->organization());

        $this->assertInstanceOf(CursorPaginator::class, $servers);
    }
}
