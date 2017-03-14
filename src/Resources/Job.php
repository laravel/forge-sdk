<?php

namespace Laravel\Forge\Resources;

class Job extends Resource
{
    /**
     * The id of the job.
     *
     * @var integer
     */
    public $id;

    /**
     * The name of the job.
     *
     * @var string
     */
    public $name;

    /**
     * The user that runs the job on the server.
     *
     * @var string
     */
    public $user;

    /**
     * How frequent the job will be running.
     *
     * @var string
     */
    public $frequency;

    /**
     * Cron representation of the job frequency.
     *
     * @var string
     */
    public $cron;

    /**
     * The status of the job.
     *
     * @var string
     */
    public $status;

    /**
     * The date/time the job was created.
     *
     * @var string
     */
    public $createdAt;
}