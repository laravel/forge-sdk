<?php

declare(strict_types=1);

namespace Tests\Integration;

use Laravel\Forge\Resources\BackgroundProcess;

class BackgroundProcessesTest extends IntegrationTestCase
{
    public function test_crud_background_process(): void
    {
        $org = $this->organization();
        $serverId = $this->serverId();
        $suffix = time();

        // Create
        $process = $this->forge()->createBackgroundProcess($org, $serverId, [
            'name' => "sdk-test-{$suffix}",
            'command' => 'sleep infinity',
            'user' => 'forge',
            'processes' => 1,
        ]);

        $this->assertInstanceOf(BackgroundProcess::class, $process);
        $this->assertIsInt($process->id);

        try {
            usleep(500_000);

            // List
            $processes = $this->forge()->backgroundProcesses($org, $serverId);
            $this->assertIsArray($processes);
            $this->assertNotEmpty($processes);

            $found = array_filter($processes, fn (BackgroundProcess $p) => $p->id === $process->id);
            $this->assertNotEmpty($found, 'Created process should appear in listing');

            usleep(500_000);

            // Read
            $fetched = $this->forge()->backgroundProcess($org, $serverId, $process->id);
            $this->assertInstanceOf(BackgroundProcess::class, $fetched);
            $this->assertSame($process->id, $fetched->id);

            // Properties
            $this->assertIsString($fetched->command);
            $this->assertNotEmpty($fetched->command);
            $this->assertIsString($fetched->status);
            $this->assertTrue(
                is_null($fetched->user) || is_string($fetched->user),
                'user should be null or string'
            );
            $this->assertTrue(
                is_null($fetched->directory) || is_string($fetched->directory),
                'directory should be null or string'
            );
            $this->assertTrue(
                is_null($fetched->processes) || is_int($fetched->processes),
                'processes should be null or int'
            );
            $this->assertTrue(
                is_null($fetched->createdAt) || is_string($fetched->createdAt),
                'createdAt should be null or string'
            );

            // Envelope keys stripped
            $this->assertArrayNotHasKey('relationships', $fetched->attributes);
            $this->assertArrayNotHasKey('links', $fetched->attributes);
        } finally {
            usleep(500_000);
            $this->forge()->deleteBackgroundProcess($org, $serverId, $process->id);
        }
    }
}
