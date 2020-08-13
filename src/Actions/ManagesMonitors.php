<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Monitor;

trait ManagesMonitors
{
    /**
     * Get the collection of monitors.
     *
     * @param  int  $serverId
     * @return \Laravel\Forge\Resources\Monitor[]
     */
    public function monitors($serverId)
    {
        return $this->transformCollection(
            $this->get("servers/$serverId/monitors")['monitors'],
            Monitor::class,
            ['server_id' => $serverId]
        );
    }

    /**
     * Get a monitor instance.
     *
     * @param  int  $serverId
     * @param  int  $monitorId
     * @return \Laravel\Forge\Resources\Monitor
     */
    public function monitor($serverId, $monitorId)
    {
        return new Monitor(
            $this->get("servers/$serverId/monitors/$monitorId")['monitor'] + ['server_id' => $serverId], $this
        );
    }

    /**
     * Create a new monitor.
     *
     * @param  int  $serverId
     * @param  array  $data
     * @return \Laravel\Forge\Resources\Monitor
     */
    public function createMonitor($serverId, array $data)
    {
        $monitor = $this->post("servers/$serverId/monitors", $data)['monitor'];

        return new Monitor($monitor + ['server_id' => $serverId], $this);
    }

    /**
     * Delete the given monitor.
     *
     * @param  int  $serverId
     * @param  int  $monitorId
     * @return void
     */
    public function deleteMonitor($serverId, $monitorId)
    {
        $this->delete("servers/$serverId/monitors/$monitorId");
    }
}
