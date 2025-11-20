<?php

namespace Laravel\Forge\Resources;

class Provider extends Resource
{
    /**
     * The id of the provider.
     *
     * @var int
     */
    public $id;

    /**
     * The name of the provider.
     *
     * @var string
     */
    public $name;

    /**
     * The label of the provider.
     *
     * @var string
     */
    public $label;

    /**
     * Determine if the provider supports load balancers.
     *
     * @var bool
     */
    public $supportsLoadBalancers;

    /**
     * Determine if the provider supports VPCs.
     *
     * @var bool
     */
    public $supportsVpcs;
}
