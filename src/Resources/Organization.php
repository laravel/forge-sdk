<?php

namespace Laravel\Forge\Resources;

class Organization extends Resource
{
    /**
     * The id of the organization.
     *
     * @var int
     */
    public $id;

    /**
     * The name of the organization.
     *
     * @var string
     */
    public $name;

    /**
     * The id of the organization owner.
     *
     * @var int
     */
    public $ownerId;

    /**
     * The date/time the organization was created.
     *
     * @var string
     */
    public $createdAt;

    /**
     * The date/time the organization was last updated.
     *
     * @var string
     */
    public $updatedAt;
}
