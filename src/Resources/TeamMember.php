<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class TeamMember extends Resource
{
    /**
     * The slug of the organization.
     */
    public string $organizationSlug;

    /**
     * The id of the team.
     */
    public ?int $teamId = null;

    /**
     * The id of the team member.
     */
    public ?int $id = null;

    /**
     * The name of the team member.
     */
    public ?string $name = null;

    /**
     * The email of the team member.
     */
    public ?string $email = null;

    /**
     * The date/time the team member was created.
     */
    public ?string $createdAt = null;

    /**
     * The date/time the team member was last updated.
     */
    public ?string $updatedAt = null;
}
