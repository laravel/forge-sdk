<?php

namespace Laravel\Forge\Resources;

class TeamInvitation extends Resource
{
    /**
     * The id of the team invitation.
     *
     * @var int
     */
    public $id;

    /**
     * The id of the team.
     *
     * @var int
     */
    public $teamId;

    /**
     * The email of the invitee.
     *
     * @var string
     */
    public $email;

    /**
     * The id of the role.
     *
     * @var int
     */
    public $roleId;

    /**
     * The date/time the team invitation was created.
     *
     * @var string
     */
    public $createdAt;

    /**
     * The date/time the team invitation expires.
     *
     * @var string
     */
    public $expiresAt;
}
