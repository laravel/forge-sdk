<?php

namespace Laravel\Forge\Resources;

class SSHKey extends Resource
{
    /**
     * The id of the key.
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
     * The name of the key.
     *
     * @var string
     */
    public $name;

    /**
     * The status of the key.
     *
     * @var string
     */
    public $status;

    /**
     * The date/time the key was created.
     *
     * @var string
     */
    public $createdAt;
}