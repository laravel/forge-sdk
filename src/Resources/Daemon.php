<?php

namespace Themsaid\Forge\Resources;

class Daemon extends Resource
{
    /**
     * The id of the daemon.
     *
     * @var integer
     */
    public $id;

    /**
     * The id of the server.
     *
     * @var integer
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
}