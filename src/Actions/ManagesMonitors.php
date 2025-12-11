<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Monitor;

trait ManagesMonitors
{
    /**
     * Get the collection of monitors.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @return \Laravel\Forge\Resources\Monitor[]
     */
    public function monitors($organizationId, $serverId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/monitors")['data'] ?? [],
            Monitor::class,
            ['organization_id' => $organizationId, 'server_id' => $serverId]
        );
    }

    /**
     * Get a monitor instance.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $monitorId
     * @return \Laravel\Forge\Resources\Monitor
     */
    public function monitor($organizationId, $serverId, $monitorId)
    {
        return new Monitor(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/monitors/{$monitorId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a new monitor.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @return \Laravel\Forge\Resources\Monitor
     */
    public function createMonitor($organizationId, $serverId, array $data)
    {
        $monitor = $this->post("orgs/{$organizationId}/servers/{$serverId}/monitors", $data)['data'] ?? [];

        return new Monitor($monitor + ['organization_id' => $organizationId, 'server_id' => $serverId], $this);
    }

    /**
     * Delete the given monitor.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $monitorId
     * @return void
     */
    public function deleteMonitor($organizationId, $serverId, $monitorId)
    {
        $this->delete("orgs/{$organizationId}/servers/{$serverId}/monitors/{$monitorId}");
    }
}
