<?php

declare(strict_types=1);

namespace Tests\Integration;

use Laravel\Forge\Resources\Deployment;
use Laravel\Forge\Resources\Site;
use Laravel\Forge\Resources\Webhook;

class DeploymentsTest extends IntegrationTestCase
{
    private function firstSite(): Site
    {
        $sites = $this->forge()->serverSites($this->organization(), $this->serverId());

        if (count($sites) === 0) {
            $this->markTestSkipped('No sites found on the test server.');
        }

        return $sites[0];
    }

    public function test_list_deployments(): void
    {
        $site = $this->firstSite();
        $deployments = $this->forge()->deployments($this->organization(), $this->serverId(), $site->id);

        $this->assertIsArray($deployments);

        if (count($deployments) === 0) {
            $this->markTestSkipped('No deployments found on the test site.');
        }

        $deployment = $deployments[0];
        $this->assertInstanceOf(Deployment::class, $deployment);
        $this->assertIsInt($deployment->id);

        $this->assertTrue(
            is_null($deployment->status) || is_string($deployment->status),
            'status should be null or string'
        );
        $this->assertTrue(
            is_null($deployment->startedAt) || is_string($deployment->startedAt),
            'startedAt should be null or string'
        );
        $this->assertTrue(
            is_null($deployment->endedAt) || is_string($deployment->endedAt),
            'endedAt should be null or string'
        );
        $this->assertTrue(
            is_null($deployment->type) || is_string($deployment->type),
            'type should be null or string'
        );
        $this->assertTrue(
            is_null($deployment->commit) || is_array($deployment->commit),
            'commit should be null or array'
        );
        $this->assertTrue(
            is_null($deployment->createdAt) || is_string($deployment->createdAt),
            'createdAt should be null or string'
        );
    }

    public function test_get_single_deployment(): void
    {
        $site = $this->firstSite();
        $deployments = $this->forge()->deployments($this->organization(), $this->serverId(), $site->id);

        if (count($deployments) === 0) {
            $this->markTestSkipped('No deployments found on the test site.');
        }

        $deployment = $this->forge()->deployment(
            $this->organization(),
            $this->serverId(),
            $site->id,
            $deployments[0]->id,
        );

        $this->assertInstanceOf(Deployment::class, $deployment);
        $this->assertSame($deployments[0]->id, $deployment->id);
    }

    public function test_deployment_has_no_jsonapi_envelope_keys(): void
    {
        $site = $this->firstSite();
        $deployments = $this->forge()->deployments($this->organization(), $this->serverId(), $site->id);

        if (count($deployments) === 0) {
            $this->markTestSkipped('No deployments found on the test site.');
        }

        $deployment = $deployments[0];
        $this->assertArrayNotHasKey('relationships', $deployment->attributes);
        $this->assertArrayNotHasKey('links', $deployment->attributes);
    }

    public function test_get_deployment_script(): void
    {
        $site = $this->firstSite();
        $script = $this->forge()->deploymentScript($this->organization(), $this->serverId(), $site->id);

        $this->assertIsString($script);
    }

    public function test_list_webhooks(): void
    {
        $site = $this->firstSite();
        $webhooks = $this->forge()->webhooks($this->organization(), $this->serverId(), $site->id);

        $this->assertIsArray($webhooks);

        if (count($webhooks) === 0) {
            // Webhooks are optional, just verify listing works.
            $this->assertTrue(true);

            return;
        }

        $webhook = $webhooks[0];
        $this->assertInstanceOf(Webhook::class, $webhook);
        $this->assertIsInt($webhook->id);

        $this->assertTrue(
            is_null($webhook->url) || is_string($webhook->url),
            'url should be null or string'
        );
    }
}
