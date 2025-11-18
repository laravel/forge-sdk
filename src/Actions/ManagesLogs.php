<?php

namespace Laravel\Forge\Actions;

trait ManagesLogs
{
    /**
     * Get server log content.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $logKey
     * @return string
     */
    public function serverLog($organizationId, $serverId, $logKey)
    {
        $response = $this->get("orgs/{$organizationId}/servers/{$serverId}/logs/{$logKey}");

        return $response['data']['content'] ?? $response['content'] ?? '';
    }

    /**
     * Delete server log content.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $logKey
     * @return void
     */
    public function deleteServerLog($organizationId, $serverId, $logKey)
    {
        $this->delete("orgs/{$organizationId}/servers/{$serverId}/logs/{$logKey}");
    }

    /**
     * Get site log content.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $logType
     * @return string
     */
    public function siteLog($organizationId, $serverId, $siteId, $logType)
    {
        $response = $this->get("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/logs/{$logType}");

        return $response['data']['content'] ?? $response['content'] ?? '';
    }

    /**
     * Delete site log content.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $logType
     * @return void
     */
    public function deleteSiteLog($organizationId, $serverId, $siteId, $logType)
    {
        $this->delete("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/logs/{$logType}");
    }
}
