<?php

declare(strict_types=1);

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Provider;
use Laravel\Forge\Resources\ProviderRegion;
use Laravel\Forge\Resources\ProviderSize;

trait ManagesProviders
{
    /**
     * Get the collection of providers.
     *
     * @return Provider[]
     */
    public function providers(): array
    {
        return $this->transformCollection(
            $this->get('providers')['data'] ?? [],
            Provider::class
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
     *
     * @return ProviderSize[]
     */
    public function providerSizes(int $providerId): array
    {
        return $this->transformCollection(
            $this->get("providers/{$providerId}/sizes")['data'] ?? [],
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
     *
     * @return ProviderRegion[]
     */
    public function providerRegions(int $providerId): array
    {
        return $this->transformCollection(
            $this->get("providers/{$providerId}/regions")['data'] ?? [],
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
     *
     * @return ProviderSize[]
     */
    public function providerRegionSizes(int $providerId, int $regionId): array
    {
        return $this->transformCollection(
            $this->get("providers/{$providerId}/regions/{$regionId}/sizes")['data'] ?? [],
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
