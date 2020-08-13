<?php

namespace Laravel\Forge\Resources;

class Monitor extends Resource
{
    /**
     * The id of the monitor.
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
     * The status of the monitor.
     *
     * @var string
     */
    public $status;

    /**
     * The type of the monitor.
     *
     * @var string
     */
    public $type;

    /**
     * The comparison operator of the monitor.
     *
     * @var string
     */
    public $operator;

    /**
     * The threshold of the monitor.
     *
     * @var integer
     */
    public $threshold;

    /**
     * The minutes of the monitor.
     *
     * @var integer
     */
    public $minutes;

    /**
     * The state of the monitor.
     *
     * @var string
     */
    public $state;

    /**
     * The state date/time of the monitor.
     *
     * @var string
     */
    public $stateChangedAt;
}

