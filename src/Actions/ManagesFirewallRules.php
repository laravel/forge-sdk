<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\FirewallRule;

trait ManagesFirewallRules
{
    /**
     * Get the collection of firewall rules.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @return \Laravel\Forge\Resources\FirewallRule[]
     */
    public function firewallRules($organizationId, $serverId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/firewall-rules")['data'] ?? [],
            FirewallRule::class,
            ['organization_id' => $organizationId, 'server_id' => $serverId]
        );
    }

    /**
     * Get a firewall rule instance.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $ruleId
     * @return \Laravel\Forge\Resources\FirewallRule
     */
    public function firewallRule($organizationId, $serverId, $ruleId)
    {
        return new FirewallRule(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/firewall-rules/{$ruleId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a new firewall rule.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @return \Laravel\Forge\Resources\FirewallRule
     */
    public function createFirewallRule($organizationId, $serverId, array $data)
    {
        $rule = $this->post("orgs/{$organizationId}/servers/{$serverId}/firewall-rules", $data)['data'] ?? [];

        return new FirewallRule($rule + ['organization_id' => $organizationId, 'server_id' => $serverId], $this);
    }

    /**
     * Delete the given firewall rule.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $ruleId
     * @return void
     */
    public function deleteFirewallRule($organizationId, $serverId, $ruleId)
    {
        $this->delete("orgs/{$organizationId}/servers/{$serverId}/firewall-rules/{$ruleId}");
    }
}
