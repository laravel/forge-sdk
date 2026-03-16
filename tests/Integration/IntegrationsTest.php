<?php

declare(strict_types=1);

namespace Tests\Integration;

use Laravel\Forge\Resources\Integration;
use Laravel\Forge\Resources\Site;

class IntegrationsTest extends IntegrationTestCase
{
    private function firstSite(): Site
    {
        $sites = $this->forge()->serverSites($this->organization(), $this->serverId());

        if (count($sites) === 0) {
            $this->markTestSkipped('No sites found on the test server.');
        }

        return $sites[0];
    }

    public function test_get_horizon(): void
    {
        $site = $this->firstSite();
        $integration = $this->forge()->getHorizon($this->organization(), $this->serverId(), $site->id);

        $this->assertInstanceOf(Integration::class, $integration);
        $this->assertSame('horizon', $integration->type);
        $this->assertTrue(
            is_null($integration->installed) || is_bool($integration->installed),
            'installed should be null or bool'
        );
    }

    public function test_get_octane(): void
    {
        $site = $this->firstSite();
        $integration = $this->forge()->getOctane($this->organization(), $this->serverId(), $site->id);

        $this->assertInstanceOf(Integration::class, $integration);
        $this->assertSame('octane', $integration->type);
        $this->assertTrue(
            is_null($integration->installed) || is_bool($integration->installed),
            'installed should be null or bool'
        );
        $this->assertTrue(
            is_null($integration->port) || is_int($integration->port),
            'port should be null or int'
        );
    }

    public function test_get_reverb(): void
    {
        $site = $this->firstSite();
        $integration = $this->forge()->getReverb($this->organization(), $this->serverId(), $site->id);

        $this->assertInstanceOf(Integration::class, $integration);
        $this->assertSame('reverb', $integration->type);
        $this->assertTrue(
            is_null($integration->installed) || is_bool($integration->installed),
            'installed should be null or bool'
        );
    }

    public function test_get_pulse(): void
    {
        $site = $this->firstSite();
        $integration = $this->forge()->getPulse($this->organization(), $this->serverId(), $site->id);

        $this->assertInstanceOf(Integration::class, $integration);
        $this->assertSame('pulse', $integration->type);
        $this->assertTrue(
            is_null($integration->installed) || is_bool($integration->installed),
            'installed should be null or bool'
        );
    }

    public function test_get_inertia(): void
    {
        $site = $this->firstSite();
        $integration = $this->forge()->getInertia($this->organization(), $this->serverId(), $site->id);

        $this->assertInstanceOf(Integration::class, $integration);
        $this->assertSame('inertia', $integration->type);
        $this->assertTrue(
            is_null($integration->installed) || is_bool($integration->installed),
            'installed should be null or bool'
        );
    }

    public function test_integration_has_no_jsonapi_envelope_keys(): void
    {
        $site = $this->firstSite();
        $integration = $this->forge()->getHorizon($this->organization(), $this->serverId(), $site->id);

        $this->assertArrayNotHasKey('relationships', $integration->attributes);
        $this->assertArrayNotHasKey('links', $integration->attributes);
    }
}
