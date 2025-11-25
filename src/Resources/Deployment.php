<?php

namespace Laravel\Forge\Resources;

class Deployment extends Resource
{
    /**
     * The id of the deployment.
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
     * The status of the deployment.
     *
     * @var string
     */
    public $status;

    /**
     * The date/time the deployment started.
     *
     * @var string
     */
    public $startedAt;

    /**
     * The date/time the deployment finished.
     *
     * @var string
     */
    public $finishedAt;

    /**
     * The output of the deployment.
     *
     * @var string
     */
    public $output;
}
