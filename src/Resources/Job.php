<?php

namespace Themsaid\Forge\Resources;

class Job extends Resource
{
    /**
     * The id of the job.
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

    /**
     * Delete the given job.
     *
     * @return void
     */
    public function delete()
    {
        $this->forge->deleteJob($this->serverId, $this->id);
    }
}