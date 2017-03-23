<?php

namespace Laravel\Forge\Resources;

class MysqlUser extends Resource
{
    /**
     * The id of the database user.
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
     * The name of the database user.
     *
     * @var string
     */
    public $name;

    /**
     * The status of the database user.
     *
     * @var string
     */
    public $status;

    /**
     * The date/time the database user was created.
     *
     * @var string
     */
    public $createdAt;
}