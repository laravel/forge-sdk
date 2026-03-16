<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class TeamMember extends Resource
{
    /**
     * The id of the team member.
     */
    public ?int $id = null;

    /**
     * The id of the team.
     */
    public ?int $teamId = null;

    /**
     * The id of the user.
     */
    public ?int $userId = null;

    /**
     * The id of the role.
     */
    public ?int $roleId = null;

    /**
     * The name of the team member.
     */
    public ?string $name = null;

    /**
     * The email of the team member.
     */
    public ?string $email = null;

    /**
     * The role of the team member.
     */
    public ?string $role = null;

    /**
     * The date/time the team member was created.
     */
    public ?string $createdAt = null;

    /**
     * The date/time the team member was last updated.
     */
    public ?string $updatedAt = null;
}
