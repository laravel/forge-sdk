<?php

declare(strict_types=1);

namespace Laravel\Forge\Actions;

use Laravel\Forge\CursorPaginator;
use Laravel\Forge\Resources\StorageProvider;

trait ManagesStorageProviders
{
    /**
     * Get the collection of storage providers for an organization.
     */
    public function storageProviders(string $organizationSlug, array $query = []): CursorPaginator
    {
        return $this->paginatedCollection(
            "orgs/{$organizationSlug}/storage-providers",
            StorageProvider::class,
            $organizationSlug,
            query: $query,
        );
    }

    /**
     * Get a storage provider instance.
     */
    public function storageProvider(string $organizationSlug, int $storageProviderId): StorageProvider
    {
        return $this->newResource(
            StorageProvider::class,
            $this->get("orgs/{$organizationSlug}/storage-providers/{$storageProviderId}")['data'] ?? [],
            $organizationSlug,
        );
    }

    /**
     * Create a new storage provider.
     */
    public function createStorageProvider(string $organizationSlug, array $data): StorageProvider
    {
        return $this->newResource(
            StorageProvider::class,
            $this->post("orgs/{$organizationSlug}/storage-providers", $data)['data'] ?? [],
            $organizationSlug,
        );
    }

    /**
     * Update a storage provider.
     */
    public function updateStorageProvider(string $organizationSlug, int $storageProviderId, array $data): StorageProvider
    {
        return $this->newResource(
            StorageProvider::class,
            $this->put("orgs/{$organizationSlug}/storage-providers/{$storageProviderId}", $data)['data'] ?? [],
            $organizationSlug,
        );
    }

    /**
     * Delete the given storage provider.
     */
    public function deleteStorageProvider(string $organizationSlug, int $storageProviderId): void
    {
        $this->delete("orgs/{$organizationSlug}/storage-providers/{$storageProviderId}");
    }
}
