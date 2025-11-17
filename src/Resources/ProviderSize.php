<?php

namespace Laravel\Forge\Resources;

class ProviderSize extends Resource
{
    /**
     * The id of the provider size.
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
     * The name of the provider size.
     *
     * @var string
     */
    public $name;

    /**
     * The label of the provider size.
     *
     * @var string
     */
    public $label;

    /**
     * The price of the provider size.
     *
     * @var string
     */
    public $price;

    /**
     * The memory of the provider size.
     *
     * @var string
     */
    public $memory;

    /**
     * The disk size of the provider size.
     *
     * @var string
     */
    public $disk;

    /**
     * The CPU of the provider size.
     *
     * @var string
     */
    public $cpu;
}
