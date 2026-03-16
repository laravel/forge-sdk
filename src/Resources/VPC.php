<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class VPC extends Resource
{
    /**
     * The id of the VPC.
     */
    public int|string|null $id = null;

    /**
     * The name of the VPC.
     */
    public ?string $name = null;

    /**
     * The region of the VPC.
     */
    public ?string $region = null;

    /**
     * The CIDR block of the VPC.
     */
    public ?string $cidrBlock = null;

    /**
     * The subnets of the VPC.
     */
    public array $subnets = [];

    /**
     * The date/time the VPC was created.
     */
    public ?string $createdAt = null;
}
