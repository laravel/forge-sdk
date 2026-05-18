<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class Organization extends Resource
{
    /**
     * The id of the organization.
     */
    public ?string $id = null;

    /**
     * The slug of the organization.
     */
    public ?string $slug = null;

    /**
     * The name of the organization.
     */
    public ?string $name = null;

    /**
     * The date/time the organization was created.
     */
    public ?string $createdAt = null;

    /**
     * The date/time the organization was last updated.
     */
    public ?string $updatedAt = null;
}
