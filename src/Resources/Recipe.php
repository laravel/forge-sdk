<?php

namespace Laravel\Forge\Resources;

class Recipe extends Resource
{
    /**
     * The id of the recipe.
     *
     * @var integer
     */
    public $id;

    /**
     * The name of the recipe.
     *
     * @var string
     */
    public $name;

    /**
     * The user that runs the recipe on the server.
     *
     * @var string
     */
    public $user;

    /**
     * The date/time the recipe was created.
     *
     * @var string
     */
    public $createdAt;
}