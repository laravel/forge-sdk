<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class TeamInvitation extends Resource
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
     * The id of the team invitation.
     */
    public ?int $id = null;

    /**
     * The email of the invitee.
     */
    public ?string $email = null;

    /**
     * The date/time the team invitation was created.
     */
    public ?string $createdAt = null;

    /**
     * The date/time the team invitation was last updated.
     */
    public ?string $updatedAt = null;
}
