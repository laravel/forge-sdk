<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class ProviderRegion extends Resource
{
    /**
     * The id of the provider region.
     */
    public ?int $id = null;

    /**
     * The id of the provider.
     */
    public ?int $providerId = null;

    /**
     * The name of the provider region.
     */
    public ?string $name = null;

    /**
     * The label of the provider region.
     */
    public ?string $label = null;

    /**
     * The code of the provider region.
     */
    public ?string $code = null;

    /**
     * The alternate code of the provider region.
     */
    public ?string $alternateCode = null;
}
