<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\SecurityRule;

trait ManagesSecurityRules
{
    /**
     * Get the collection of security rules.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\SecurityRule[]
     */
    public function securityRules($organizationId, $serverId, $siteId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/security-rules")['data'] ?? [],
            SecurityRule::class,
            ['organization_id' => $organizationId, 'server_id' => $serverId, 'site_id' => $siteId]
        );
    }

    /**
     * Get a security rule instance.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $ruleId
     * @return \Laravel\Forge\Resources\SecurityRule
     */
    public function securityRule($organizationId, $serverId, $siteId, $ruleId)
    {
        return new SecurityRule(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/security-rules/{$ruleId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a new security rule.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  array  $data
     * @return \Laravel\Forge\Resources\SecurityRule
     */
    public function createSecurityRule($organizationId, $serverId, $siteId, array $data)
    {
        $rule = $this->post(
            "orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/security-rules",
            $data
        )['data'] ?? [];

        return new SecurityRule(
            $rule + ['organization_id' => $organizationId, 'server_id' => $serverId, 'site_id' => $siteId],
            $this
        );
    }

    /**
     * Update a security rule.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $ruleId
     * @param  array  $data
     * @return \Laravel\Forge\Resources\SecurityRule
     */
    public function updateSecurityRule($organizationId, $serverId, $siteId, $ruleId, array $data)
    {
        $rule = $this->put(
            "orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/security-rules/{$ruleId}",
            $data
        )['data'] ?? [];

        return new SecurityRule($rule, $this);
    }

    /**
     * Delete the given security rule.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $ruleId
     * @return void
     */
    public function deleteSecurityRule($organizationId, $serverId, $siteId, $ruleId)
    {
        $this->delete("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/security-rules/{$ruleId}");
    }
}
