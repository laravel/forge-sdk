<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class TeamInvitation extends Resource
{
    /**
     * The id of the team invitation.
     */
    public ?int $id = null;

    /**
     * The id of the team.
     */
    public ?int $teamId = null;

    /**
     * The email of the invitee.
     */
    public ?string $email = null;

    /**
     * The id of the role.
     */
    public ?int $roleId = null;

    /**
     * The date/time the team invitation was created.
     */
    public ?string $createdAt = null;

    /**
     * The date/time the team invitation expires.
     */
    public ?string $expiresAt = null;

    /**
     * The date/time the team invitation was last updated.
     */
    public ?string $updatedAt = null;
}
