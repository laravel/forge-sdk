<?php

namespace Laravel\Forge\Resources;

class Site extends Resource
{
    /**
     * The id of the site.
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
     * The name of the site.
     *
     * @var string
     */
    public $name;

    /**
     * The name of the directory housing the site.
     *
     * @var string
     */
    public $directory;

    /**
     * Determine if the site allows Wildcard Sub-Domains.
     *
     * @var boolean
     */
    public $wildcards;

    /**
     * The status of the site.
     *
     * @var string
     */
    public $status;

    /**
     * The deployment git repository.
     *
     * @var string
     */
    public $repository;

    /**
     * The type of the repository provider.
     *
     * @var string
     */
    public $repositoryProvider;

    /**
     * The name of the deployment branch.
     *
     * @var string
     */
    public $repositoryBranch;

    /**
     * The status of the repository.
     *
     * @var string
     */
    public $repositoryStatus;

    /**
     * Determine if "Quick Deploy" is enabled for the site.
     *
     * @var boolean
     */
    public $quickDeploy;

    /**
     * The type of the project installed on the site.
     *
     * @var string
     */
    public $projectType;

    /**
     * The application installed on the site.
     *
     * @var string
     */
    public $app;

    /**
     * The status of the application installed.
     *
     * @var string
     */
    public $appStatus;

    /**
     * Hipchat room for deployment notifications.
     *
     * @var string
     */
    public $hipchatRoom;

    /**
     * Slack channel for deployment notifications.
     *
     * @var string
     */
    public $slackChannel;

    /**
     * The status of load balancing.
     *
     * @var string
     */
    public $balancingStatus;

    /**
     * The The date/time the site was created.
     *
     * @var string
     */
    public $createdAt;
}

