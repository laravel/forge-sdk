<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\FirewallRule;

trait ManagesFirewallRules
{
    /**
     * Get the collection of firewall rules.
     *
     * @param  int  $serverId
     * @return \Laravel\Forge\Resources\FirewallRule[]
     */
    public function firewallRules($serverId)
    {
        return $this->transformCollection(
            $this->get("servers/$serverId/firewall-rules")['rules'],
            FirewallRule::class,
            ['server_id' => $serverId]
        );
    }

    /**
     * Get a firewall rule instance.
     *
     * @param  int  $serverId
     * @param  int  $ruleId
     * @return \Laravel\Forge\Resources\FirewallRule
     */
    public function firewallRule($serverId, $ruleId)
    {
        return new FirewallRule(
            $this->get("servers/$serverId/firewall-rules/$ruleId")['rule'] + ['server_id' => $serverId], $this
        );
    }

    /**
     * Create a new firewall rule.
     *
     * @param  int  $serverId
     * @param  array  $data
     * @param  bool  $wait
     * @return \Laravel\Forge\Resources\FirewallRule
     */
    public function createFirewallRule($serverId, array $data, $wait = true)
    {
        $rule = $this->post("servers/$serverId/firewall-rules", $data)['rule'];

        if ($wait) {
            return $this->retry($this->getTimeout(), function () use ($serverId, $rule) {
                $rule = $this->firewallRule($serverId, $rule['id']);

                return $rule->status == 'installed' ? $rule : null;
            });
        }

        return new FirewallRule($rule + ['server_id' => $serverId], $this);
    }

    /**
     * Delete the given firewall rule.
     *
     * @param  int  $serverId
     * @param  int  $ruleId
     * @return void
     */
    public function deleteFirewallRule($serverId, $ruleId)
    {
        $this->delete("servers/$serverId/firewall-rules/$ruleId");
    }
}
