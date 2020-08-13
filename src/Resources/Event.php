<?php

namespace Laravel\Forge\Resources;

class Event extends Resource
{

    /**
     * The id of the server where the event occured.
     *
     * @var integer
     */
    public $serverId;

    /**
     * The user that ran the event.
     *
     * @var string
     */
    public $ranAs;

    /**
     * The name of the server the event occured.
     *
     * @var string
     */
    public $serverName;

    /**
     * The description of the event.
     *
     * @var string
     */
    public $description;

    /**
     * The date/time the job was created.
     *
     * @var string
     */
    public $createdAt;

}
