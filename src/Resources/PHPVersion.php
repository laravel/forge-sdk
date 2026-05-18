<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class PHPVersion extends Resource
{
    /**
     * The id of the organization.
     */
    public ?string $organizationId = null;

    /**
     * The id of the server.
     */
    public ?int $serverId = null;

    /**
     * The id of the PHP version.
     */
    public ?int $id = null;

    /**
     * The version of PHP.
     */
    public ?string $version = null;

    /**
     * The binary name of PHP.
     */
    public ?string $binaryName = null;

    /**
     * The status of the version.
     */
    public ?string $status = null;

    /**
     * The date/time the PHP version was created.
     */
    public ?string $createdAt = null;

    /**
     * The date/time the PHP version was last updated.
     */
    public ?string $updatedAt = null;
}
