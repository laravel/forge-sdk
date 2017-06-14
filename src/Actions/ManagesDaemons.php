<?php

namespace Themsaid\Forge\Actions;

use Themsaid\Forge\Resources\Daemon;

trait ManagesDaemons
{
    /**
     * Get the collection of daemons.
     *
     * @param  integer $serverId
     * @return Daemon[]
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
     * @param  integer $serverId
     * @param  integer $daemonId
     * @return Daemon
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
     * @param  integer $serverId
     * @param  array $data
     * @param  boolean $wait
     * @return Daemon
     */
    public function createDaemon($serverId, array $data, $wait = true)
    {
        $daemon = $this->post("servers/$serverId/daemons", $data)['daemon'];

        if ($wait) {
            return $this->retry(30, function () use ($serverId, $daemon) {
                $daemon = $this->daemon($serverId, $daemon['id']);

                return $daemon->status == 'installed' ? $daemon : null;
            });
        }

        return new Daemon($daemon + ['server_id' => $serverId], $this);
    }

    /**
     * Restart the given daemon.
     *
     * @param  integer $serverId
     * @param  integer $daemonId
     * @param  boolean $wait
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
     * @param  integer $serverId
     * @param  integer $daemonId
     * @return void
     */
    public function deleteDaemon($serverId, $daemonId)
    {
        $this->delete("servers/$serverId/daemons/$daemonId");
    }
}
