<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\RedirectRule;

trait ManagesRedirectRules
{
    /**
     * Get the collection of redirect rules.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @return \Laravel\Forge\Resources\RedirectRule[]
     */
    public function redirectRules($serverId, $siteId)
    {
        return $this->transformCollection(
            $this->get("servers/$serverId/sites/$siteId/redirect-rules")['redirect_rules'],
            RedirectRule::class,
            ['server_id' => $serverId, 'site_id' => $siteId]
        );
    }

    /**
     * Get a redirect rule instance.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @param  int  $ruleId
     * @return \Laravel\Forge\Resources\RedirectRule
     */
    public function redirectRule($serverId, $siteId, $ruleId)
    {
        return new RedirectRule(
            $this->get("servers/$serverId/sites/$siteId/redirect-rules/$ruleId")['redirect_rule']
            + ['server_id' => $serverId, 'site_id' => $siteId], $this
        );
    }

    /**
     * Create a new redirect rule.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @param  array  $data
     * @param  bool  $wait
     * @return \Laravel\Forge\Resources\RedirectRule
     */
    public function createRedirectRule($serverId, $siteId, array $data, $wait = true)
    {
        $redirectRule = $this->post("servers/$serverId/sites/$siteId/redirect-rules", $data)['redirect_rule'];

        if ($wait) {
            return $this->retry($this->getTimeout(), function () use ($serverId, $siteId, $redirectRule) {
                $redirectRule = $this->redirectRule($serverId, $siteId, $redirectRule['id']);

                return $redirectRule->status == 'installed' ? $redirectRule : null;
            });
        }

        return new RedirectRule($redirectRule + ['server_id' => $serverId, 'site_id' => $siteId], $this);
    }

    /**
     * Delete the given redirect rule.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @param  int  $ruleId
     * @return void
     */
    public function deleteRedirectRule($serverId, $siteId, $ruleId)
    {
        $this->delete("servers/$serverId/sites/$siteId/redirect-rules/$ruleId");
    }
}
