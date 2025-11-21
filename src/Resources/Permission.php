<?php

namespace Laravel\Forge\Resources;

class Permission extends Resource
{
    /**
     * The id of the permission.
     *
     * @var int
     */
    public $id;

    /**
     * The name of the permission.
     *
     * @var string
     */
    public $name;

    /**
     * The description of the permission.
     *
     * @var string
     */
    public $description;

    /**
     * The category of the permission.
     *
     * @var string
     */
    public $category;
}
