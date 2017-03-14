<?php

namespace Laravel\Forge\Resources;

class MysqlDatabase extends Resource
{
    /**
     * The id of the database.
     *
     * @var integer
     */
    public $id;

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