<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\RedirectRule;

trait ManagesRedirectRules
{
    /**
     * Get the collection of redirect rules.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\RedirectRule[]
     */
    public function redirectRules($organizationId, $serverId, $siteId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/redirect-rules")['data'] ?? [],
            RedirectRule::class,
            ['organization_id' => $organizationId, 'server_id' => $serverId, 'site_id' => $siteId]
        );
    }

    /**
     * Get a redirect rule instance.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $ruleId
     * @return \Laravel\Forge\Resources\RedirectRule
     */
    public function redirectRule($organizationId, $serverId, $siteId, $ruleId)
    {
        return new RedirectRule(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/redirect-rules/{$ruleId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a new redirect rule.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\RedirectRule
     */
    public function createRedirectRule($organizationId, $serverId, $siteId, array $data)
    {
        $rule = $this->post(
            "orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/redirect-rules",
            $data
        )['data'] ?? [];

        return new RedirectRule(
            $rule + ['organization_id' => $organizationId, 'server_id' => $serverId, 'site_id' => $siteId],
            $this
        );
    }

    /**
     * Delete the given redirect rule.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $ruleId
     * @return void
     */
    public function deleteRedirectRule($organizationId, $serverId, $siteId, $ruleId)
    {
        $this->delete("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/redirect-rules/{$ruleId}");
    }
}
