<?php

namespace Laravel\Forge\Resources;

class Role extends Resource
{
    /**
     * The id of the role.
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
     * The name of the role.
     *
     * @var string
     */
    public $name;

    /**
     * The description of the role.
     *
     * @var string
     */
    public $description;

    /**
     * The date/time the role was created.
     *
     * @var string
     */
    public $createdAt;

    /**
     * The date/time the role was last updated.
     *
     * @var string
     */
    public $updatedAt;
}
