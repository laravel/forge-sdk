<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\FirewallRule;

trait ManagesFirewallRules
{
    /**
     * Get the collection of firewall rules.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return \Laravel\Forge\Resources\FirewallRule[]
     */
    public function firewallRules($organizationSlug, $serverId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/firewall-rules")['data'] ?? [],
            FirewallRule::class,
            ['organization_id' => $organizationSlug, 'server_id' => $serverId]
        );
    }

    /**
     * Get a firewall rule instance.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $ruleId
     * @return \Laravel\Forge\Resources\FirewallRule
     */
    public function firewallRule($organizationSlug, $serverId, $ruleId)
    {
        return new FirewallRule(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/firewall-rules/{$ruleId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a new firewall rule.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return \Laravel\Forge\Resources\FirewallRule
     */
    public function createFirewallRule($organizationSlug, $serverId, array $data)
    {
        $rule = $this->post("orgs/{$organizationSlug}/servers/{$serverId}/firewall-rules", $data)['data'] ?? [];

        return new FirewallRule($rule + ['organization_id' => $organizationSlug, 'server_id' => $serverId], $this);
    }

    /**
     * Delete the given firewall rule.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $ruleId
     * @return void
     */
    public function deleteFirewallRule($organizationSlug, $serverId, $ruleId)
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/firewall-rules/{$ruleId}");
    }
}
