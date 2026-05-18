<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class Team extends Resource
{
    /**
     * The id of the team.
     */
    public ?int $id = null;

    /**
     * The id of the organization.
     */
    public ?string $organizationId = null;

    /**
     * The name of the team.
     */
    public ?string $name = null;

    /**
     * The date/time the team was created.
     */
    public ?string $createdAt = null;

    /**
     * The date/time the team was last updated.
     */
    public ?string $updatedAt = null;
}
