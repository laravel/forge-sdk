<?php

declare(strict_types=1);

namespace Laravel\Forge\Actions;

use Laravel\Forge\CursorPaginator;
use Laravel\Forge\Resources\ForgeRecipe;
use Laravel\Forge\Resources\Recipe;
use Laravel\Forge\Resources\RecipeRun;

trait ManagesRecipes
{
    /**
     * Get the collection of recipes for an organization.
     */
    public function recipes(string $organizationSlug, array $query = []): CursorPaginator
    {
        return $this->paginatedCollection(
            "orgs/{$organizationSlug}/recipes",
            Recipe::class,
            $organizationSlug,
            query: $query,
        );
    }

    /**
     * Get a recipe.
     */
    public function recipe(string $organizationSlug, int $recipeId): Recipe
    {
        return $this->newResource(
            Recipe::class,
            $this->get("orgs/{$organizationSlug}/recipes/{$recipeId}")['data'] ?? [],
            $organizationSlug,
        );
    }

    /**
     * Create a new recipe.
     */
    public function createRecipe(string $organizationSlug, array $data): Recipe
    {
        return $this->newResource(
            Recipe::class,
            $this->post("orgs/{$organizationSlug}/recipes", $data)['data'] ?? [],
            $organizationSlug,
        );
    }

    /**
     * Update a recipe.
     */
    public function updateRecipe(string $organizationSlug, int $recipeId, array $data): Recipe
    {
        return $this->newResource(
            Recipe::class,
            $this->put("orgs/{$organizationSlug}/recipes/{$recipeId}", $data)['data'] ?? [],
            $organizationSlug,
        );
    }

    /**
     * Delete a recipe.
     */
    public function deleteRecipe(string $organizationSlug, int $recipeId): void
    {
        $this->delete("orgs/{$organizationSlug}/recipes/{$recipeId}");
    }

    /**
     * Get the collection of recipe runs for a recipe.
     */
    public function recipeRuns(string $organizationSlug, int $recipeId, array $query = []): CursorPaginator
    {
        return $this->paginatedCollection(
            "orgs/{$organizationSlug}/recipes/{$recipeId}/runs",
            RecipeRun::class,
            $organizationSlug,
            extra: ['recipe_id' => $recipeId],
            query: $query,
        );
    }

    /**
     * Get a recipe run.
     */
    public function recipeRun(string $organizationSlug, int $recipeId, int $logId): RecipeRun
    {
        return $this->newResource(
            RecipeRun::class,
            $this->get("orgs/{$organizationSlug}/recipes/{$recipeId}/runs/{$logId}")['data'] ?? [],
            $organizationSlug,
            extra: ['recipe_id' => $recipeId],
        );
    }

    /**
     * Create a new recipe run.
     */
    public function createRecipeRun(string $organizationSlug, int $recipeId, array $data): void
    {
        $this->post("orgs/{$organizationSlug}/recipes/{$recipeId}/runs", $data);
    }

    /**
     * Get the collection of recipes for a team.
     */
    public function teamRecipes(string $organizationSlug, int $teamId, array $query = []): CursorPaginator
    {
        return $this->paginatedCollection(
            "orgs/{$organizationSlug}/teams/{$teamId}/recipes",
            Recipe::class,
            $organizationSlug,
            extra: ['team_id' => $teamId],
            query: $query,
        );
    }

    /**
     * Share a recipe with a team (create team recipes share).
     */
    public function createTeamRecipesShare(string $organizationSlug, int $teamId, array $data): Recipe
    {
        return $this->newResource(
            Recipe::class,
            $this->post("orgs/{$organizationSlug}/teams/{$teamId}/recipes", $data)['data'] ?? [],
            $organizationSlug,
            extra: ['team_id' => $teamId],
        );
    }

    /**
     * Remove a recipe share from a team (delete team recipes share).
     */
    public function deleteTeamRecipesShare(string $organizationSlug, int $teamId, int $recipeId): void
    {
        $this->delete("orgs/{$organizationSlug}/teams/{$teamId}/recipes/{$recipeId}");
    }

    /**
     * Get the collection of Forge recipes.
     */
    public function forgeRecipes(array $query = []): CursorPaginator
    {
        return $this->paginatedCollection(
            'forge-recipes',
            ForgeRecipe::class,
            query: $query,
        );
    }

    /**
     * Get a Forge recipe.
     */
    public function forgeRecipe(int $forgeRecipeId): ForgeRecipe
    {
        return new ForgeRecipe($this->get("forge-recipes/{$forgeRecipeId}")['data'] ?? [], $this);
    }

    /**
     * Create a Forge recipe run.
     */
    public function createForgeRecipeRun(int $forgeRecipeId, array $data): void
    {
        $this->post("forge-recipes/{$forgeRecipeId}/runs", $data);
    }
}
