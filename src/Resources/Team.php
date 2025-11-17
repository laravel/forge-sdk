<?php

namespace Laravel\Forge\Resources;

class Team extends Resource
{
    /**
     * The id of the team.
     *
     * @var int
     */
    public $id;

    /**
     * The id of the organization.
     *
     * @var int
     */
    public $organizationId;

    /**
     * The name of the team.
     *
     * @var string
     */
    public $name;

    /**
     * The description of the team.
     *
     * @var string
     */
    public $description;

    /**
     * The date/time the team was created.
     *
     * @var string
     */
    public $createdAt;

    /**
     * The date/time the team was last updated.
     *
     * @var string
     */
    public $updatedAt;
}
