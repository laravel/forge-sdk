<?php

namespace Laravel\Forge\Resources;

class Command extends Resource
{
    /**
     * The id of the command.
     *
     * @var int
     */
    public $id;

    /**
     * The id of the site.
     *
     * @var int
     */
    public $siteId;

    /**
     * The command.
     *
     * @var string
     */
    public $command;

    /**
     * The status of the command.
     *
     * @var string
     */
    public $status;

    /**
     * The output of the command.
     *
     * @var string
     */
    public $output;

    /**
     * The date/time the command was created.
     *
     * @var string
     */
    public $createdAt;
}
