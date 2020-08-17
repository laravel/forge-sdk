<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\SecurityRule;

trait ManagesSecurityRules
{
    /**
     * Get the collection of security rules.
     *
     * @param  int $serverId
     * @param  int $siteId
     * @return \Laravel\Forge\Resources\SecurityRule[]
     */
    public function securityRules($serverId, $siteId)
    {
        return $this->transformCollection(
            $this->get("servers/$serverId/sites/$siteId/security-rules")['security_rules'],
            SecurityRule::class,
            ['server_id' => $serverId, 'site_id' => $siteId]
        );
    }

    /**
     * Get a security rule instance.
     *
     * @param  int $serverId
     * @param  int $siteId
     * @param  int $ruleId
     * @return \Laravel\Forge\Resources\SecurityRule
     */
    public function securityRule($serverId, $siteId, $ruleId)
    {
        return new SecurityRule(
            $this->get("servers/$serverId/sites/$siteId/security-rules/$ruleId")['security_rule']
            + ['server_id' => $serverId, 'site_id' => $siteId], $this
        );
    }

    /**
     * Create a new security rule.
     *
     * @param  int $serverId
     * @param  int $siteId
     * @param  array $data
     * @param  boolean $wait
     * @return \Laravel\Forge\Resources\SecurityRule
     */
    public function createSecurityRule($serverId, $siteId, array $data, $wait = true)
    {
        $securityRule = $this->post("servers/$serverId/sites/$siteId/security-rules", $data)['security_rule'];

        if ($wait) {
            return $this->retry($this->getTimeout(), function () use ($serverId, $siteId, $securityRule) {
                $securityRule = $this->securityRule($serverId, $siteId, $securityRule['id']);

                return $securityRule->status == 'installed' ? $securityRule : null;
            });
        }

        return new SecurityRule($securityRule + ['server_id' => $serverId, 'site_id' => $siteId], $this);
    }

    /**
     * Delete the given security rule.
     *
     * @param  int $serverId
     * @param  int $siteId
     * @param  int $ruleId
     * @return void
     */
    public function deleteSecurityRule($serverId, $siteId, $ruleId)
    {
        $this->delete("servers/$serverId/sites/$siteId/security-rules/$ruleId");
    }
}
