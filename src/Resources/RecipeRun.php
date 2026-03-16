<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class RecipeRun extends Resource
{
    /**
     * The id of the recipe run.
     */
    public ?int $id = null;

    /**
     * The id of the recipe.
     */
    public ?int $recipeId = null;

    /**
     * The id of the forge recipe.
     */
    public ?int $forgeRecipeId = null;

    /**
     * The id of the server.
     */
    public ?int $serverId = null;

    /**
     * The status of the recipe run.
     */
    public ?string $status = null;

    /**
     * The output of the recipe run.
     */
    public ?string $output = null;

    /**
     * The user who executed the recipe run.
     */
    public ?string $executedBy = null;

    /**
     * The date/time the recipe run started.
     */
    public ?string $startedAt = null;

    /**
     * The date/time the recipe run finished.
     */
    public ?string $finishedAt = null;

    /**
     * The date/time the recipe run was created.
     */
    public ?string $createdAt = null;
}
