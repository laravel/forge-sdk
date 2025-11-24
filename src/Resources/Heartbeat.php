<?php

namespace Laravel\Forge\Resources;

class Heartbeat extends Resource
{
    /**
     * The id of the heartbeat.
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
     * The id of the site.
     *
     * @var int
     */
    public $siteId;

    /**
     * The name of the heartbeat.
     *
     * @var string
     */
    public $name;

    /**
     * The status of the heartbeat.
     *
     * @var string
     */
    public $status;

    /**
     * The interval of the heartbeat.
     *
     * @var int
     */
    public $interval;

    /**
     * The date/time the heartbeat was created.
     *
     * @var string
     */
    public $createdAt;

    /**
     * Delete the given heartbeat.
     *
     * @return void
     */
    public function delete()
    {
        $this->forge->deleteHeartbeat($this->serverId, $this->siteId, $this->id);
    }
}
