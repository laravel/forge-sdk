<?php

declare(strict_types=1);

namespace Tests\Integration;

use Laravel\Forge\CursorPaginator;
use Laravel\Forge\Resources\Command;
use Laravel\Forge\Resources\Site;

class CommandsTest extends IntegrationTestCase
{
    private function firstSite(): Site
    {
        $sites = $this->forge()->serverSites($this->organization(), $this->serverId());

        if (count($sites) === 0) {
            $this->markTestSkipped('No sites found on the test server.');
        }

        return $sites[0];
    }

    public function test_create_and_list_commands(): void
    {
        $org = $this->organization();
        $serverId = $this->serverId();
        $site = $this->firstSite();

        // Create a command
        $this->forge()->createCommand($org, $serverId, $site->id, [
            'command' => 'echo "SDK integration test"',
        ]);

        usleep(500_000);

        // List should now have at least one command
        $commands = $this->forge()->commands($org, $serverId, $site->id);
        $this->assertInstanceOf(CursorPaginator::class, $commands);
        $this->assertNotEmpty($commands, 'Commands list should not be empty after creating one');
        $this->assertContainsOnlyInstancesOf(Command::class, $commands);

        $first = $commands[0];
        $this->assertInstanceOf(Command::class, $first);
        $this->assertIsInt($first->id);

        $this->assertTrue(
            is_null($first->command) || is_string($first->command),
            'command should be null or string'
        );
        $this->assertTrue(
            is_null($first->status) || is_string($first->status),
            'status should be null or string'
        );
        $this->assertTrue(
            is_null($first->createdAt) || is_string($first->createdAt),
            'createdAt should be null or string'
        );

        // Envelope keys stripped
        $this->assertIsArray($first->relationships);
        $this->assertIsArray($first->links);
    }
}
