<?php

namespace Themsaid\Forge\Resources;

class MysqlDatabase extends Resource
{
    /**
     * The id of the database.
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
     * The name of the database.
     *
     * @var string
     */
    public $name;

    /**
     * The status of the database.
     *
     * @var string
     */
    public $status;

    /**
     * The date/time the database was created.
     *
     * @var string
     */
    public $createdAt;
}