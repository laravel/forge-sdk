<?php

declare(strict_types=1);

namespace Tests\Integration;

use Laravel\Forge\Resources\Monitor;

class MonitorsTest extends IntegrationTestCase
{
    private static ?int $testMonitorId = null;

    /**
     * Create a monitor for testing.
     */
    private function ensureTestMonitor(): void
    {
        if (static::$testMonitorId !== null) {
            return;
        }

        $this->forge()->createMonitor($this->organization(), $this->serverId(), [
            'type' => 'disk',
            'operator' => 'gte',
            'threshold' => 80,
            'minutes' => '5',
            'notify' => 'sdk-test@example.com',
        ]);

        sleep(2);
        $monitors = $this->forge()->monitors($this->organization(), $this->serverId());
        foreach ($monitors as $m) {
            if ($m->notify === 'sdk-test@example.com') {
                static::$testMonitorId = $m->id;

                return;
            }
        }
        $this->markTestSkipped('Monitor creation was accepted but monitor did not appear in listing.');
    }

    public static function tearDownAfterClass(): void
    {
        if (static::$testMonitorId !== null && static::$forge !== null) {
            try {
                static::$forge->deleteMonitor(static::$organization, static::$serverId, static::$testMonitorId);
            } catch (\Throwable) {
                // Best-effort cleanup.
            }
            static::$testMonitorId = null;
        }

        parent::tearDownAfterClass();
    }

    public function test_list_monitors(): void
    {
        $this->ensureTestMonitor();

        $monitors = $this->forge()->monitors($this->organization(), $this->serverId());

        $this->assertIsArray($monitors);
        $this->assertNotEmpty($monitors);

        $monitor = $monitors[0];
        $this->assertInstanceOf(Monitor::class, $monitor);
        $this->assertIsInt($monitor->id);
        $this->assertIsString($monitor->type);
        $this->assertIsString($monitor->status);

        // v2 properties
        $this->assertTrue(
            is_null($monitor->operator) || is_string($monitor->operator),
            'operator should be null or string'
        );
        $this->assertTrue(
            is_null($monitor->threshold) || is_int($monitor->threshold),
            'threshold should be null or int'
        );
        $this->assertTrue(
            is_null($monitor->notify) || is_string($monitor->notify),
            'notify should be null or string'
        );
        $this->assertTrue(
            is_null($monitor->createdAt) || is_string($monitor->createdAt),
            'createdAt should be null or string'
        );
        $this->assertTrue(
            is_null($monitor->updatedAt) || is_string($monitor->updatedAt),
            'updatedAt should be null or string'
        );

        // Envelope keys stripped (type is kept — it's a domain attribute, not the JSON:API type)
        $this->assertArrayNotHasKey('relationships', $monitor->attributes);
        $this->assertArrayNotHasKey('links', $monitor->attributes);
    }
}
