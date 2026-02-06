<?php

namespace Laravel\Forge\Actions;

trait ManagesLogs
{
    /**
     * Get server log content.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $logKey
     * @return string
     */
    public function serverLog($organizationSlug, $serverId, $logKey)
    {
        $response = $this->get("orgs/{$organizationSlug}/servers/{$serverId}/logs/{$logKey}");

        return $response['data']['content'] ?? $response['content'] ?? '';
    }

    /**
     * Delete server log content.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $logKey
     * @return void
     */
    public function deleteServerLog($organizationSlug, $serverId, $logKey)
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/logs/{$logKey}");
    }

    /**
     * Get site log content.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $logKey
     * @return string
     */
    public function siteLog($organizationSlug, $serverId, $siteId, $logKey)
    {
        $response = $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/logs/{$logKey}");

        return $response['data']['content'] ?? $response['content'] ?? '';
    }

    /**
     * Delete site log content.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $logKey
     * @return void
     */
    public function deleteSiteLog($organizationSlug, $serverId, $siteId, $logKey)
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/logs/{$logKey}");
    }
}
