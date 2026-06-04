<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class Role extends Resource
{
    /**
     * The id of the role.
     */
    public ?int $id = null;

    /**
     * The slug of the organization.
     */
    public string $organizationSlug;

    /**
     * The name of the role.
     */
    public ?string $name = null;

    /**
     * The date/time the role was created.
     */
    public ?string $createdAt = null;

    /**
     * The date/time the role was last updated.
     */
    public ?string $updatedAt = null;
}
