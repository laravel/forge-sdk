<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class ForgeRecipe extends Resource
{
    /**
     * The id of the recipe.
     */
    public ?int $id = null;

    /**
     * The name of the recipe.
     */
    public ?string $name = null;

    /**
     * The description of the recipe.
     */
    public ?string $description = null;

    /**
     * The script of the recipe.
     */
    public ?string $script = null;

    /**
     * The user of the recipe.
     */
    public ?string $user = null;

    /**
     * The info of the recipe.
     */
    public ?string $info = null;

    /**
     * The date/time the recipe was created.
     */
    public ?string $createdAt = null;

    /**
     * The date/time the recipe was last updated.
     */
    public ?string $updatedAt = null;
}
