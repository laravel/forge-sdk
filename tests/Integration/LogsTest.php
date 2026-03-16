<?php

declare(strict_types=1);

namespace Tests\Integration;

use Laravel\Forge\Exceptions\NotFoundException;

class LogsTest extends IntegrationTestCase
{
    public function test_get_server_log(): void
    {
        try {
            $log = $this->forge()->serverLog($this->organization(), $this->serverId(), 'ssh');
        } catch (NotFoundException) {
            $this->markTestSkipped('The "ssh" log is not available on the test server.');
        }

        $this->assertIsString($log);
    }
}
