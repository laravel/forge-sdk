<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class Provider extends Resource
{
    /**
     * The id of the provider.
     */
    public ?int $id = null;

    /**
     * The name of the provider.
     */
    public ?string $name = null;

    /**
     * The slug of the provider.
     */
    public ?string $slug = null;

    /**
     * The simple name of the provider.
     */
    public ?string $simpleName = null;

    /**
     * The currency of the provider.
     */
    public ?string $currency = null;

    /**
     * The currency symbol of the provider.
     */
    public ?string $currencySymbol = null;

    /**
     * The default size code of the provider.
     */
    public ?string $defaultSizeCode = null;

    /**
     * The default region code of the provider.
     */
    public ?string $defaultRegionCode = null;
}
