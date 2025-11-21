<?php

namespace Laravel\Forge\Resources;

class TeamMember extends Resource
{
    /**
     * The id of the team member.
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
     * The id of the user.
     *
     * @var int
     */
    public $userId;

    /**
     * The id of the role.
     *
     * @var int
     */
    public $roleId;

    /**
     * The name of the team member.
     *
     * @var string
     */
    public $name;

    /**
     * The email of the team member.
     *
     * @var string
     */
    public $email;

    /**
     * The date/time the team member was created.
     *
     * @var string
     */
    public $createdAt;
}
