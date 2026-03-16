<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class Integration extends Resource
{
    /**
     * The id of the integration.
     */
    public ?int $id = null;

    /**
     * The id of the organization.
     */
    public ?string $organizationId = null;

    /**
     * The id of the server.
     */
    public ?int $serverId = null;

    /**
     * The id of the site.
     */
    public ?int $siteId = null;

    /**
     * The type of the integration.
     */
    public ?string $type = null;

    /**
     * The status of the integration.
     */
    public ?string $status = null;

    /**
     * Whether the integration is installed.
     */
    public ?bool $installed = null;

    /**
     * Whether the integration is enabled.
     */
    public ?bool $enabled = null;

    /**
     * The port of the integration (Octane, Reverb).
     */
    public ?int $port = null;

    /**
     * The host of the integration (Reverb).
     */
    public ?string $host = null;

    /**
     * The number of connections (Reverb).
     */
    public ?int $connections = null;

    /**
     * The date/time the integration was created.
     */
    public ?string $createdAt = null;
}
