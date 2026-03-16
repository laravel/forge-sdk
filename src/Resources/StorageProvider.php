<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class StorageProvider extends Resource
{
    /**
     * The id of the storage provider.
     */
    public ?int $id = null;

    /**
     * The id of the organization.
     */
    public ?string $organizationId = null;

    /**
     * The name of the storage provider.
     */
    public ?string $name = null;

    /**
     * The type of the storage provider.
     */
    public ?string $type = null;

    /**
     * The provider type of the storage provider.
     */
    public ?string $provider = null;

    /**
     * The provider display name of the storage provider.
     */
    public ?string $providerName = null;

    /**
     * The region of the storage provider.
     */
    public ?string $region = null;

    /**
     * The bucket of the storage provider.
     */
    public ?string $bucket = null;

    /**
     * The directory of the storage provider.
     */
    public ?string $directory = null;

    /**
     * The endpoint of the storage provider.
     */
    public ?string $endpoint = null;

    /**
     * The assume role ARN of the storage provider.
     */
    public ?string $assumeRole = null;

    /**
     * Whether the storage provider is in use.
     */
    public ?bool $inUse = null;

    /**
     * The date/time the storage provider was created.
     */
    public ?string $createdAt = null;

    /**
     * The date/time the storage provider was last updated.
     */
    public ?string $updatedAt = null;
}
