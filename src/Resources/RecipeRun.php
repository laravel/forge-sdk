<?php

namespace Laravel\Forge\Resources;

class RecipeRun extends Resource
{
    /**
     * The id of the recipe run.
     *
     * @var int
     */
    public $id;

    /**
     * The id of the recipe.
     *
     * @var int
     */
    public $recipeId;

    /**
     * The id of the server.
     *
     * @var int
     */
    public $serverId;

    /**
     * The status of the recipe run.
     *
     * @var string
     */
    public $status;

    /**
     * The output of the recipe run.
     *
     * @var string
     */
    public $output;

    /**
     * The date/time the recipe run was created.
     *
     * @var string
     */
    public $createdAt;
}
