<?php

namespace Laravel\Forge\Resources;

class ProviderRegion extends Resource
{
    /**
     * The id of the provider region.
     *
     * @var int
     */
    public $id;

    /**
     * The id of the provider.
     *
     * @var int
     */
    public $providerId;

    /**
     * The name of the provider region.
     *
     * @var string
     */
    public $name;

    /**
     * The label of the provider region.
     *
     * @var string
     */
    public $label;
}
