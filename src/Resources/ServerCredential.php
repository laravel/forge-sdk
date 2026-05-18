<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class ServerCredential extends Resource
{
    /**
     * The id of the server credential.
     */
    public ?int $id = null;

    /**
     * The id of the organization.
     */
    public ?string $organizationId = null;

    /**
     * The name of the server credential.
     */
    public ?string $name = null;

    /**
     * The provider of the server credential.
     */
    public ?string $provider = null;

    /**
     * Whether the server credential is in use.
     */
    public ?bool $inUse = null;

    /**
     * The date/time the server credential was created.
     */
    public ?string $createdAt = null;

    /**
     * The date/time the server credential was last updated.
     */
    public ?string $updatedAt = null;
}
