<?php

declare(strict_types=1);

namespace Laravel\Forge\Actions;

use Laravel\Forge\CursorPaginator;
use Laravel\Forge\Resources\Provider;
use Laravel\Forge\Resources\ProviderRegion;
use Laravel\Forge\Resources\ProviderSize;

trait ManagesProviders
{
    /**
     * Get the collection of providers.
     */
    public function providers(): CursorPaginator
    {
        return $this->paginatedCollection(
            'providers',
            Provider::class,
        );
    }

    /**
     * Get a specific provider.
     */
    public function provider(int $providerId): Provider
    {
        return new Provider($this->get("providers/{$providerId}")['data'] ?? [], $this);
    }

    /**
     * Get the collection of sizes for a provider.
     */
    public function providerSizes(int $providerId): CursorPaginator
    {
        return $this->paginatedCollection(
            "providers/{$providerId}/sizes",
            ProviderSize::class,
            extra: ['provider_id' => $providerId],
        );
    }

    /**
     * Get a specific provider size.
     */
    public function providerSize(int $providerId, int $sizeId): ProviderSize
    {
        return new ProviderSize(
            $this->get("providers/{$providerId}/sizes/{$sizeId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Get the collection of regions for a provider.
     */
    public function providerRegions(int $providerId): CursorPaginator
    {
        return $this->paginatedCollection(
            "providers/{$providerId}/regions",
            ProviderRegion::class,
            extra: ['provider_id' => $providerId],
        );
    }

    /**
     * Get a specific provider region.
     */
    public function providerRegion(int $providerId, int $regionId): ProviderRegion
    {
        return new ProviderRegion(
            $this->get("providers/{$providerId}/regions/{$regionId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Get the collection of sizes for a specific region.
     */
    public function providerRegionSizes(int $providerId, int $regionId): CursorPaginator
    {
        return $this->paginatedCollection(
            "providers/{$providerId}/regions/{$regionId}/sizes",
            ProviderSize::class,
            extra: ['provider_id' => $providerId, 'region_id' => $regionId],
        );
    }

    /**
     * Get a specific size for a specific region.
     */
    public function providerRegionSize(int $providerId, int $regionId, int $sizeId): ProviderSize
    {
        return new ProviderSize(
            $this->get("providers/{$providerId}/regions/{$regionId}/sizes/{$sizeId}")['data'] ?? [],
            $this
        );
    }
}
