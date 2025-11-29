<?php

namespace Laravel\Forge\Resources;

class ScheduledJob extends Resource
{
    /**
     * The id of the scheduled job.
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
     * The command of the scheduled job.
     *
     * @var string
     */
    public $command;

    /**
     * The user running the scheduled job.
     *
     * @var string
     */
    public $user;

    /**
     * The frequency of the scheduled job.
     *
     * @var string
     */
    public $frequency;

    /**
     * The status of the scheduled job.
     *
     * @var string
     */
    public $status;

    /**
     * The date/time the scheduled job was created.
     *
     * @var string
     */
    public $createdAt;
}
