<?php

namespace Laravel\Forge\Resources;

class BackgroundProcess extends Resource
{
    /**
     * The id of the background process.
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
     * The command of the background process.
     *
     * @var string
     */
    public $command;

    /**
     * The user running the background process.
     *
     * @var string
     */
    public $user;

    /**
     * The status of the background process.
     *
     * @var string
     */
    public $status;

    /**
     * The date/time the background process was created.
     *
     * @var string
     */
    public $createdAt;
}
