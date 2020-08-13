<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Daemon;

trait ManagesDaemons
{
    /**
     * Get the collection of daemons.
     *
     * @param  int  $serverId
     * @return \Laravel\Forge\Resources\Daemon[]
     */
    public function daemons($serverId)
    {
        return $this->transformCollection(
            $this->get("servers/$serverId/daemons")['daemons'],
            Daemon::class,
            ['server_id' => $serverId]
        );
    }

    /**
     * Get a daemon instance.
     *
     * @param  int  $serverId
     * @param  int  $daemonId
     * @return \Laravel\Forge\Resources\Daemon
     */
    public function daemon($serverId, $daemonId)
    {
        return new Daemon(
            $this->get("servers/$serverId/daemons/$daemonId")['daemon'] + ['server_id' => $serverId], $this
        );
    }

    /**
     * Create a new daemon.
     *
     * @param  int  $serverId
     * @param  array  $data
     * @param  bool  $wait
     * @return \Laravel\Forge\Resources\Daemon
     */
    public function createDaemon($serverId, array $data, $wait = true)
    {
        $daemon = $this->post("servers/$serverId/daemons", $data)['daemon'];

        if ($wait) {
            return $this->retry($this->getTimeout(), function () use ($serverId, $daemon) {
                $daemon = $this->daemon($serverId, $daemon['id']);

                return $daemon->status == 'installed' ? $daemon : null;
            });
        }

        return new Daemon($daemon + ['server_id' => $serverId], $this);
    }

    /**
     * Restart the given daemon.
     *
     * @param  int  $serverId
     * @param  int  $daemonId
     * @param  bool  $wait
     * @return void
     */
    public function restartDaemon($serverId, $daemonId, $wait = true)
    {
        $this->post("servers/$serverId/daemons/$daemonId/restart");

        if ($wait) {
            $this->retry(30, function () use ($serverId, $daemonId) {
                $daemon = $this->daemon($serverId, $daemonId);

                return $daemon->status == 'installed';
            });
        }
    }

    /**
     * Delete the given daemon.
     *
     * @param  int  $serverId
     * @param  int  $daemonId
     * @return void
     */
    public function deleteDaemon($serverId, $daemonId)
    {
        $this->delete("servers/$serverId/daemons/$daemonId");
    }
}
