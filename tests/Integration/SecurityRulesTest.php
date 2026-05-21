<?php

declare(strict_types=1);

namespace Tests\Integration;

use Laravel\Forge\Resources\SecurityRule;
use Laravel\Forge\Resources\Site;

class SecurityRulesTest extends IntegrationTestCase
{
    private function firstSite(): Site
    {
        $sites = $this->forge()->serverSites($this->organization(), $this->serverId());

        if (count($sites) === 0) {
            $this->markTestSkipped('No sites found on the test server.');
        }

        return $sites[0];
    }

    public function test_crud_security_rule(): void
    {
        $org = $this->organization();
        $serverId = $this->serverId();
        $site = $this->firstSite();
        $suffix = time();

        // Create
        $rule = $this->forge()->createSecurityRule($org, $serverId, $site->id, [
            'name' => "SDK Test Rule {$suffix}",
            'path' => "/sdk-test-{$suffix}",
            'credentials' => [
                [
                    'username' => 'sdktest',
                    'password' => "TestPass{$suffix}!",
                ],
            ],
        ]);

        $this->assertInstanceOf(SecurityRule::class, $rule);
        $this->assertIsInt($rule->id);
        $this->assertSame("SDK Test Rule {$suffix}", $rule->name);

        try {
            usleep(500_000);

            // List
            $rules = $this->forge()->securityRules($org, $serverId, $site->id);
            $this->assertIsArray($rules);
            $this->assertNotEmpty($rules);

            $found = array_filter($rules, fn (SecurityRule $r) => $r->id === $rule->id);
            $this->assertNotEmpty($found, 'Created rule should appear in listing');

            usleep(500_000);

            // Read
            $fetched = $this->forge()->securityRule($org, $serverId, $site->id, $rule->id);
            $this->assertInstanceOf(SecurityRule::class, $fetched);
            $this->assertSame($rule->id, $fetched->id);

            // Properties
            $this->assertTrue(
                is_null($fetched->name) || is_string($fetched->name),
                'name should be null or string'
            );
            $this->assertTrue(
                is_null($fetched->path) || is_string($fetched->path),
                'path should be null or string'
            );
            $this->assertTrue(
                is_null($fetched->status) || is_string($fetched->status),
                'status should be null or string'
            );
            $this->assertTrue(
                is_null($fetched->createdAt) || is_string($fetched->createdAt),
                'createdAt should be null or string'
            );

            // Envelope keys stripped
            $this->assertIsArray($fetched->relationships);
            $this->assertIsArray($fetched->links);
        } finally {
            usleep(500_000);
            $this->forge()->deleteSecurityRule($org, $serverId, $site->id, $rule->id);
        }
    }
}
