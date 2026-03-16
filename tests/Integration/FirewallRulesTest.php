<?php

declare(strict_types=1);

namespace Tests\Integration;

use Laravel\Forge\Resources\FirewallRule;

class FirewallRulesTest extends IntegrationTestCase
{
    public function test_list_firewall_rules(): void
    {
        $rules = $this->forge()->firewallRules($this->organization(), $this->serverId());

        $this->assertIsArray($rules);

        if (count($rules) === 0) {
            $this->markTestSkipped('No firewall rules found on the test server.');
        }

        $rule = $rules[0];
        $this->assertInstanceOf(FirewallRule::class, $rule);
        $this->assertIsInt($rule->id);
        $this->assertIsString($rule->name);
        $this->assertNotEmpty($rule->name);
        $this->assertIsString($rule->status);
        $this->assertIsString($rule->createdAt, 'createdAt should be hydrated');

        // v2 properties
        $this->assertTrue(
            is_null($rule->port) || is_int($rule->port),
            'port should be null or int'
        );
        $this->assertTrue(
            is_null($rule->type) || is_string($rule->type),
            'type should be null or string'
        );
        $this->assertTrue(
            is_null($rule->ipAddress) || is_string($rule->ipAddress),
            'ipAddress should be null or string'
        );
        $this->assertTrue(
            is_null($rule->updatedAt) || is_string($rule->updatedAt),
            'updatedAt should be null or string'
        );
    }

    public function test_firewall_rule_has_no_jsonapi_envelope_keys(): void
    {
        $rules = $this->forge()->firewallRules($this->organization(), $this->serverId());

        if (count($rules) === 0) {
            $this->markTestSkipped('No firewall rules found on the test server.');
        }

        $rule = $rules[0];
        $this->assertArrayNotHasKey('relationships', $rule->attributes);
        $this->assertArrayNotHasKey('links', $rule->attributes);
    }
}
