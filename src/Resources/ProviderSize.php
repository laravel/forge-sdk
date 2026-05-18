<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class ProviderSize extends Resource
{
    /**
     * The id of the provider size.
     */
    public ?int $id = null;

    /**
     * The id of the provider.
     */
    public ?int $providerId = null;

    /**
     * The id of the region (when fetched via the region-specific endpoint).
     */
    public ?int $regionId = null;

    /**
     * The name of the provider size.
     */
    public ?string $name = null;

    /**
     * The code of the provider size.
     */
    public ?string $code = null;

    /**
     * The series of the provider size.
     */
    public ?string $series = null;

    /**
     * The category of the provider size.
     */
    public ?string $category = null;

    /**
     * The number of CPUs.
     */
    public ?int $cpus = null;

    /**
     * The disk size of the provider size.
     */
    public ?int $disk = null;

    /**
     * The disk type of the provider size.
     */
    public ?string $diskType = null;

    /**
     * The architecture of the provider size.
     */
    public ?string $architecture = null;

    /**
     * The RAM of the provider size.
     */
    public ?int $ram = null;
}
