<?php

namespace Laravel\Forge\Resources;

class Integration extends Resource
{
    /**
     * The id of the integration.
     *
     * @var int|null
     */
    public $id;

    /**
     * The id of the organization.
     *
     * @var int
     */
    public $organizationId;

    /**
     * The id of the server.
     *
     * @var int
     */
    public $serverId;

    /**
     * The id of the site.
     *
     * @var int
     */
    public $siteId;

    /**
     * The type of the integration.
     *
     * @var string
     */
    public $type;

    /**
     * The status of the integration.
     *
     * @var string|null
     */
    public $status;

    /**
     * Whether the integration is installed.
     *
     * @var bool|null
     */
    public $installed;

    /**
     * The date/time the integration was created.
     *
     * @var string|null
     */
    public $createdAt;
}
