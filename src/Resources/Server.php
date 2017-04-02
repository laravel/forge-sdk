<?php

namespace Themsaid\Forge\Resources;

class Server extends Resource
{
    /**
     * The id of the recipe.
     *
     * @var integer
     */
    public $id;

    /**
     * The name of the recipe.
     *
     * @var string
     */
    public $name;

    /**
     * The id of the provider credential instance.
     *
     * @var integer
     */
    public $credentialId;

    /**
     * The size of the server.
     *
     * @var string
     */
    public $size;

    /**
     * The region of the server.
     *
     * @var string
     */
    public $region;

    /**
     * The IP address of the server.
     *
     * @var string
     */
    public $ipAddress;

    /**
     * The Private IP address of the server.
     *
     * @var string
     */
    public $privateIpAddress;

    /**
     * The PHP version used in the server.
     *
     * @var string
     */
    public $phpVersion;

    /**
     * The status of the Blackfire service.
     *
     * @var string
     */
    public $blackfireStatus;

    /**
     * The status of the Papertrail service.
     *
     * @var string
     */
    public $papertrailStatus;

    /**
     * Determine if the server installation is done.
     *
     * @var bool
     */
    public $isReady;

    /**
     * Determine if Forge access to the server was revoked.
     *
     * @var bool
     */
    public $revoked;

    /**
     * The date/time the server was created.
     *
     * @var string
     */
    public $createdAt;

    /**
     * The IDs of other servers on the same servers network.
     *
     * @var array
     */
    public $network = [];
}
