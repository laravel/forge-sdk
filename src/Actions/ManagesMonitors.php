<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Monitor;

trait ManagesMonitors
{
    /**
     * Get the collection of monitors.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return \Laravel\Forge\Resources\Monitor[]
     */
    public function monitors($organizationSlug, $serverId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/monitors")['data'] ?? [],
            Monitor::class,
            ['organization_id' => $organizationSlug, 'server_id' => $serverId]
        );
    }

    /**
     * Get a monitor instance.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $monitorId
     * @return \Laravel\Forge\Resources\Monitor
     */
    public function monitor($organizationSlug, $serverId, $monitorId)
    {
        return new Monitor(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/monitors/{$monitorId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a new monitor.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return \Laravel\Forge\Resources\Monitor
     */
    public function createMonitor($organizationSlug, $serverId, array $data)
    {
        $monitor = $this->post("orgs/{$organizationSlug}/servers/{$serverId}/monitors", $data)['data'] ?? [];

        return new Monitor($monitor + ['organization_id' => $organizationSlug, 'server_id' => $serverId], $this);
    }

    /**
     * Delete the given monitor.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $monitorId
     * @return void
     */
    public function deleteMonitor($organizationSlug, $serverId, $monitorId)
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/monitors/{$monitorId}");
    }
}
