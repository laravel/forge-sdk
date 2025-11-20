<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Provider;
use Laravel\Forge\Resources\ProviderRegion;
use Laravel\Forge\Resources\ProviderSize;

trait ManagesProviders
{
    /**
     * Get the collection of providers.
     *
     * @return \Laravel\Forge\Resources\Provider[]
     */
    public function providers()
    {
        return $this->transformCollection(
            $this->get('providers')['data'] ?? [],
            Provider::class
        );
    }

    /**
     * Get a specific provider.
     *
     * @param  string  $providerId
     * @return \Laravel\Forge\Resources\Provider
     */
    public function provider($providerId)
    {
        return new Provider($this->get("providers/{$providerId}")['data'] ?? [], $this);
    }

    /**
     * Get the collection of sizes for a provider.
     *
     * @param  string  $providerId
     * @return \Laravel\Forge\Resources\ProviderSize[]
     */
    public function providerSizes($providerId)
    {
        return $this->transformCollection(
            $this->get("providers/{$providerId}/sizes")['data'] ?? [],
            ProviderSize::class,
            ['provider_id' => $providerId]
        );
    }

    /**
     * Get a specific provider size.
     *
     * @param  string  $providerId
     * @param  string  $sizeId
     * @return \Laravel\Forge\Resources\ProviderSize
     */
    public function providerSize($providerId, $sizeId)
    {
        return new ProviderSize(
            $this->get("providers/{$providerId}/sizes/{$sizeId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Get the collection of regions for a provider.
     *
     * @param  string  $providerId
     * @return \Laravel\Forge\Resources\ProviderRegion[]
     */
    public function providerRegions($providerId)
    {
        return $this->transformCollection(
            $this->get("providers/{$providerId}/regions")['data'] ?? [],
            ProviderRegion::class,
            ['provider_id' => $providerId]
        );
    }

    /**
     * Get a specific provider region.
     *
     * @param  string  $providerId
     * @param  string  $regionId
     * @return \Laravel\Forge\Resources\ProviderRegion
     */
    public function providerRegion($providerId, $regionId)
    {
        return new ProviderRegion(
            $this->get("providers/{$providerId}/regions/{$regionId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Get the collection of sizes for a specific region.
     *
     * @param  string  $providerId
     * @param  string  $regionId
     * @return \Laravel\Forge\Resources\ProviderSize[]
     */
    public function providerRegionSizes($providerId, $regionId)
    {
        return $this->transformCollection(
            $this->get("providers/{$providerId}/regions/{$regionId}/sizes")['data'] ?? [],
            ProviderSize::class,
            ['provider_id' => $providerId, 'region_id' => $regionId]
        );
    }

    /**
     * Get a specific size for a specific region.
     *
     * @param  string  $providerId
     * @param  string  $regionId
     * @param  string  $sizeId
     * @return \Laravel\Forge\Resources\ProviderSize
     */
    public function providerRegionSize($providerId, $regionId, $sizeId)
    {
        return new ProviderSize(
            $this->get("providers/{$providerId}/regions/{$regionId}/sizes/{$sizeId}")['data'] ?? [],
            $this
        );
    }
}
