<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\StorageProvider;

trait ManagesStorageProviders
{
    /**
     * Get the collection of storage providers for an organization.
     *
     * @param  string  $organizationSlug
     * @return \Laravel\Forge\Resources\StorageProvider[]
     */
    public function storageProviders($organizationSlug)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/storage-providers")['data'] ?? [],
            StorageProvider::class,
            ['organization_id' => $organizationSlug]
        );
    }

    /**
     * Get a storage provider instance.
     *
     * @param  string  $organizationSlug
     * @param  string  $storageProviderId
     * @return \Laravel\Forge\Resources\StorageProvider
     */
    public function storageProvider($organizationSlug, $storageProviderId)
    {
        return new StorageProvider(
            $this->get("orgs/{$organizationSlug}/storage-providers/{$storageProviderId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a new storage provider.
     *
     * @param  string  $organizationSlug
     * @return \Laravel\Forge\Resources\StorageProvider
     */
    public function createStorageProvider($organizationSlug, array $data)
    {
        $provider = $this->post("orgs/{$organizationSlug}/storage-providers", $data)['data'] ?? [];

        return new StorageProvider($provider + ['organization_id' => $organizationSlug], $this);
    }

    /**
     * Update a storage provider.
     *
     * @param  string  $organizationSlug
     * @param  string  $storageProviderId
     * @return \Laravel\Forge\Resources\StorageProvider
     */
    public function updateStorageProvider($organizationSlug, $storageProviderId, array $data)
    {
        $provider = $this->put(
            "orgs/{$organizationSlug}/storage-providers/{$storageProviderId}",
            $data
        )['data'] ?? [];

        return new StorageProvider($provider, $this);
    }

    /**
     * Delete the given storage provider.
     *
     * @param  string  $organizationSlug
     * @param  string  $storageProviderId
     * @return void
     */
    public function deleteStorageProvider($organizationSlug, $storageProviderId)
    {
        $this->delete("orgs/{$organizationSlug}/storage-providers/{$storageProviderId}");
    }
}
