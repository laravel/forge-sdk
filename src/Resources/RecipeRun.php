<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class RecipeRun extends Resource
{
    /**
     * The id of the organization.
     */
    public ?string $organizationId = null;

    /**
     * The id of the recipe.
     */
    public ?int $recipeId = null;

    /**
     * The id of the recipe run.
     */
    public ?int $id = null;

    /**
     * The id of the server the recipe was run against.
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
     * The id of the user who executed the recipe run.
     */
    public ?int $executedBy = null;

    /**
     * The date/time the recipe run started.
     */
    public ?string $startedAt = null;

    /**
     * The date/time the recipe run finished.
     */
    public ?string $finishedAt = null;
}
