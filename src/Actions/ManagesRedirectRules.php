<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\RedirectRule;

trait ManagesRedirectRules
{
    /**
     * Get the collection of redirect rules.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\RedirectRule[]
     */
    public function redirectRules($organizationSlug, $serverId, $siteId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/redirect-rules")['data'] ?? [],
            RedirectRule::class,
            ['organization_id' => $organizationSlug, 'server_id' => $serverId, 'site_id' => $siteId]
        );
    }

    /**
     * Get a redirect rule instance.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $ruleId
     * @return \Laravel\Forge\Resources\RedirectRule
     */
    public function redirectRule($organizationSlug, $serverId, $siteId, $ruleId)
    {
        return new RedirectRule(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/redirect-rules/{$ruleId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a new redirect rule.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\RedirectRule
     */
    public function createRedirectRule($organizationSlug, $serverId, $siteId, array $data)
    {
        $rule = $this->post(
            "orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/redirect-rules",
            $data
        )['data'] ?? [];

        return new RedirectRule(
            $rule + ['organization_id' => $organizationSlug, 'server_id' => $serverId, 'site_id' => $siteId],
            $this
        );
    }

    /**
     * Delete the given redirect rule.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $ruleId
     * @return void
     */
    public function deleteRedirectRule($organizationSlug, $serverId, $siteId, $ruleId)
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/redirect-rules/{$ruleId}");
    }
}
