<?php

namespace Themsaid\Forge\Resources;

class Worker extends Resource
{
    /**
     * The id of the worker.
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
     * The id of the site.
     *
     * @var integer
     */
    public $siteId;

    /**
     * The connection name the worker uses.
     *
     * @var string
     */
    public $connection;

    /**
     * The command for running the worker.
     *
     * @var string
     */
    public $command;

    /**
     * The queue name.
     *
     * @var string
     */
    public $queue;

    /**
     * The number of seconds a child process can run.
     *
     * @var integer
     */
    public $timeout;

    /**
     * The number of seconds to sleep when no job is available.
     *
     * @var integer
     */
    public $sleep;

    /**
     * The number of times to attempt a job before logging it failed.
     *
     * @var integer
     */
    public $tries;

    /**
     * The name of the environment.
     *
     * @var string
     */
    public $environment;

    /**
     * Determine if the worker is in daemon mode.
     *
     * @var integer
     */
    public $daemon;

    /**
     * The status of the worker.
     *
     * @var string
     */
    public $status;

    /**
     * The date/time the worker was created.
     *
     * @var string
     */
    public $createdAt;

    /**
     * Delete the given worker.
     *
     * @return void
     */
    public function delete()
    {
        return $this->forge->deleteWorker($this->serverId, $this->siteId, $this->id);
    }

    /**
     * Restart the given worker.
     *
     * @param  boolean $wait
     * @return void
     */
    public function restartWorker($wait = true)
    {
        return $this->forge->restartWorker($this->serverId, $this->siteId, $this->id, $wait);
    }
}