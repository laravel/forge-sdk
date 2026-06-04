<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class VPC extends Resource
{
    /**
     * The slug of the organization.
     */
    public string $organizationSlug;

    /**
     * The id of the server credential used to fetch the VPC.
     */
    public ?int $credentialId = null;

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
}
