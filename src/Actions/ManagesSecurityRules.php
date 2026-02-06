<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\SecurityRule;

trait ManagesSecurityRules
{
    /**
     * Get the collection of security rules.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\SecurityRule[]
     */
    public function securityRules($organizationSlug, $serverId, $siteId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/security-rules")['data'] ?? [],
            SecurityRule::class,
            ['organization_id' => $organizationSlug, 'server_id' => $serverId, 'site_id' => $siteId]
        );
    }

    /**
     * Get a security rule instance.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $ruleId
     * @return \Laravel\Forge\Resources\SecurityRule
     */
    public function securityRule($organizationSlug, $serverId, $siteId, $ruleId)
    {
        return new SecurityRule(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/security-rules/{$ruleId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a new security rule.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\SecurityRule
     */
    public function createSecurityRule($organizationSlug, $serverId, $siteId, array $data)
    {
        $rule = $this->post(
            "orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/security-rules",
            $data
        )['data'] ?? [];

        return new SecurityRule(
            $rule + ['organization_id' => $organizationSlug, 'server_id' => $serverId, 'site_id' => $siteId],
            $this
        );
    }

    /**
     * Update a security rule.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $ruleId
     * @return \Laravel\Forge\Resources\SecurityRule
     */
    public function updateSecurityRule($organizationSlug, $serverId, $siteId, $ruleId, array $data)
    {
        $rule = $this->put(
            "orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/security-rules/{$ruleId}",
            $data
        )['data'] ?? [];

        return new SecurityRule($rule, $this);
    }

    /**
     * Delete the given security rule.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $ruleId
     * @return void
     */
    public function deleteSecurityRule($organizationSlug, $serverId, $siteId, $ruleId)
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/security-rules/{$ruleId}");
    }
}
