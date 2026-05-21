<?php

declare(strict_types=1);

namespace Tests\Integration;

use Laravel\Forge\Resources\RedirectRule;
use Laravel\Forge\Resources\Site;

class RedirectRulesTest extends IntegrationTestCase
{
    private function firstSite(): Site
    {
        $sites = $this->forge()->serverSites($this->organization(), $this->serverId());

        if (count($sites) === 0) {
            $this->markTestSkipped('No sites found on the test server.');
        }

        return $sites[0];
    }

    public function test_list_redirect_rules(): void
    {
        $site = $this->firstSite();
        $rules = $this->forge()->redirectRules($this->organization(), $this->serverId(), $site->id);

        $this->assertIsArray($rules);

        if (count($rules) === 0) {
            $this->markTestSkipped('No redirect rules found on the test site.');
        }

        $rule = $rules[0];
        $this->assertInstanceOf(RedirectRule::class, $rule);
        $this->assertIsInt($rule->id);

        $this->assertTrue(
            is_null($rule->from) || is_string($rule->from),
            'from should be null or string'
        );
        $this->assertTrue(
            is_null($rule->to) || is_string($rule->to),
            'to should be null or string'
        );
        $this->assertTrue(
            is_null($rule->type) || is_string($rule->type),
            'type should be null or string'
        );
        $this->assertTrue(
            is_null($rule->status) || is_string($rule->status),
            'status should be null or string'
        );
        $this->assertTrue(
            is_null($rule->createdAt) || is_string($rule->createdAt),
            'createdAt should be null or string'
        );
        $this->assertTrue(
            is_null($rule->updatedAt) || is_string($rule->updatedAt),
            'updatedAt should be null or string'
        );
    }

    public function test_redirect_rule_has_no_jsonapi_envelope_keys(): void
    {
        $site = $this->firstSite();
        $rules = $this->forge()->redirectRules($this->organization(), $this->serverId(), $site->id);

        if (count($rules) === 0) {
            $this->markTestSkipped('No redirect rules found on the test site.');
        }

        $rule = $rules[0];
        $this->assertIsArray($rule->relationships);
        $this->assertIsArray($rule->links);
    }
}
