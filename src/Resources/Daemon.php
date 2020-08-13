<?php

namespace Laravel\Forge\Resources;

class Daemon extends Resource
{
    /**
     * The id of the daemon.
     *
     * @var int
     */
    public $id;

    /**
     * The id of the server.
     *
     * @var int
     */
    public $serverId;

    /**
     * The command the daemon runs.
     *
     * @var string
     */
    public $command;

    /**
     * The user that runs the daemon on the server.
     *
     * @var string
     */
    public $user;

    /**
     * The status of the daemon.
     *
     * @var string
     */
    public $status;

    /**
     * The date/time the daemon was created.
     *
     * @var string
     */
    public $createdAt;

    /**
     * Restart the given daemon.
     *
     * @param  bool  $wait
     * @return void
     */
    public function restart($wait = true)
    {
        $this->forge->restartDaemon($this->serverId, $this->id, $wait);
    }

    /**
     * Delete the given daemon.
     *
     * @return void
     */
    public function delete()
    {
        $this->forge->deleteDaemon($this->serverId, $this->id);
    }
}
