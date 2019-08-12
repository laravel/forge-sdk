<?php

namespace Themsaid\Forge\Actions;

use Themsaid\Forge\Resources\RedirectRule;

trait ManagesRedirectRules
{
    /**
     * Get the collection of redirect rules.
     *
     * @param  integer $serverId
     * @param  integer $siteId
     * @return RedirectRule[]
     */
    public function redirectRules($serverId, $siteId)
    {
        return $this->transformCollection(
            $this->get("servers/$serverId/sites/$siteId/redirect-rules")['redirect-rules'],
            RedirectRule::class,
            ['server_id' => $serverId, 'site_id' => $siteId]
        );
    }

    /**
     * Get a redirect rule instance.
     *
     * @param  integer $serverId
     * @param  integer $siteId
     * @param  integer $ruleId
     * @return RedirectRule
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
     * @param  integer $serverId
     * @param  integer $siteId
     * @param  array $data
     * @param  boolean $wait
     * @return RedirectRule
     */
    public function createRedirectRule($serverId, $siteId, array $data, $wait = true)
    {
        $worker = $this->post("servers/$serverId/sites/$siteId/redirect-rules", $data)['redirect_rule'];

        if ($wait) {
            return $this->retry($this->getTimeout(), function () use ($serverId, $siteId, $redirectRule) {
                $redirectRule = $this->redirectRule($serverId, $siteId, $redirectRule['id']);

                return $redirectRule->status == 'installed' ? $worker : null;
            });
        }

        return new RedirectRule($redirectRule + ['server_id' => $serverId, 'site_id' => $siteId], $this);
    }

    /**
     * Delete the given redirect rule.
     *
     * @param  integer $serverId
     * @param  integer $siteId
     * @param  integer $ruleId
     * @return void
     */
    public function deleteRedirectRule($serverId, $siteId, $ruleId)
    {
        $this->delete("servers/$serverId/sites/$siteId/redirect-rules/$ruleId");
    }
}