<?php

namespace Laravel\Forge\Resources;

class ForgeRecipe extends Resource
{
    /**
     * The id of the recipe.
     *
     * @var int
     */
    public $id;

    /**
     * The name of the recipe.
     *
     * @var string
     */
    public $name;

    /**
     * The description of the recipe.
     *
     * @var string
     */
    public $description;

    /**
     * The script of the recipe.
     *
     * @var string
     */
    public $script;

    /**
     * The date/time the recipe was created.
     *
     * @var string
     */
    public $createdAt;
}
