<?php

namespace Laravel\Forge\Resources;

class VPC extends Resource
{
    /**
     * The id of the VPC.
     *
     * @var int
     */
    public $id;

    /**
     * The name of the VPC.
     *
     * @var string
     */
    public $name;

    /**
     * The region of the VPC.
     *
     * @var string
     */
    public $region;

    /**
     * The CIDR block of the VPC.
     *
     * @var string
     */
    public $cidrBlock;

    /**
     * The date/time the VPC was created.
     *
     * @var string
     */
    public $createdAt;
}
