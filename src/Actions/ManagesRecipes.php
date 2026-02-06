<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\ForgeRecipe;
use Laravel\Forge\Resources\Recipe;
use Laravel\Forge\Resources\RecipeRun;

trait ManagesRecipes
{
    /**
     * Get the collection of recipes for an organization.
     *
     * @param  string  $organizationSlug
     * @return \Laravel\Forge\Resources\Recipe[]
     */
    public function recipes($organizationSlug)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/recipes")['data'] ?? [],
            Recipe::class,
            ['organization_id' => $organizationSlug]
        );
    }

    /**
     * Get a recipe.
     *
     * @param  string  $organizationSlug
     * @param  string  $recipeId
     * @return \Laravel\Forge\Resources\Recipe
     */
    public function recipe($organizationSlug, $recipeId)
    {
        return new Recipe($this->get("orgs/{$organizationSlug}/recipes/{$recipeId}")['data'] ?? [], $this);
    }

    /**
     * Create a new recipe.
     *
     * @param  string  $organizationSlug
     * @return \Laravel\Forge\Resources\Recipe
     */
    public function createRecipe($organizationSlug, array $data)
    {
        $recipe = $this->post("orgs/{$organizationSlug}/recipes", $data)['data'] ?? [];

        return new Recipe($recipe + ['organization_id' => $organizationSlug], $this);
    }

    /**
     * Update a recipe.
     *
     * @param  string  $organizationSlug
     * @param  string  $recipeId
     * @return \Laravel\Forge\Resources\Recipe
     */
    public function updateRecipe($organizationSlug, $recipeId, array $data)
    {
        $recipe = $this->put("orgs/{$organizationSlug}/recipes/{$recipeId}", $data)['data'] ?? [];

        return new Recipe($recipe, $this);
    }

    /**
     * Delete a recipe.
     *
     * @param  string  $organizationSlug
     * @param  string  $recipeId
     * @return void
     */
    public function deleteRecipe($organizationSlug, $recipeId)
    {
        $this->delete("orgs/{$organizationSlug}/recipes/{$recipeId}");
    }

    /**
     * Get the collection of recipe runs for a recipe.
     *
     * @param  string  $organizationSlug
     * @param  string  $recipeId
     * @return \Laravel\Forge\Resources\RecipeRun[]
     */
    public function recipeRuns($organizationSlug, $recipeId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/recipes/{$recipeId}/runs")['data'] ?? [],
            RecipeRun::class,
            ['organization_id' => $organizationSlug, 'recipe_id' => $recipeId]
        );
    }

    /**
     * Get a recipe run.
     *
     * @param  string  $organizationSlug
     * @param  string  $recipeId
     * @param  string  $logId
     * @return \Laravel\Forge\Resources\RecipeRun
     */
    public function recipeRun($organizationSlug, $recipeId, $logId)
    {
        return new RecipeRun(
            $this->get("orgs/{$organizationSlug}/recipes/{$recipeId}/runs/{$logId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a new recipe run.
     *
     * @param  string  $organizationSlug
     * @param  string  $recipeId
     * @return \Laravel\Forge\Resources\RecipeRun
     */
    public function createRecipeRun($organizationSlug, $recipeId, array $data)
    {
        $run = $this->post("orgs/{$organizationSlug}/recipes/{$recipeId}/runs", $data)['data'] ?? [];

        return new RecipeRun($run + ['organization_id' => $organizationSlug, 'recipe_id' => $recipeId], $this);
    }

    /**
     * Get the collection of recipes for a team.
     *
     * @param  string  $organizationSlug
     * @param  string  $teamId
     * @return \Laravel\Forge\Resources\Recipe[]
     */
    public function teamRecipes($organizationSlug, $teamId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/teams/{$teamId}/recipes")['data'] ?? [],
            Recipe::class,
            ['organization_id' => $organizationSlug, 'team_id' => $teamId]
        );
    }

    /**
     * Share a recipe with a team (create team recipes share).
     *
     * @param  string  $organizationSlug
     * @param  string  $teamId
     * @return \Laravel\Forge\Resources\Recipe
     */
    public function createTeamRecipesShare($organizationSlug, $teamId, array $data)
    {
        $recipe = $this->post("orgs/{$organizationSlug}/teams/{$teamId}/recipes", $data)['data'] ?? [];

        return new Recipe($recipe + ['organization_id' => $organizationSlug, 'team_id' => $teamId], $this);
    }

    /**
     * Remove a recipe share from a team (delete team recipes share).
     *
     * @param  string  $organizationSlug
     * @param  string  $teamId
     * @param  string  $recipeId
     * @return void
     */
    public function deleteTeamRecipesShare($organizationSlug, $teamId, $recipeId)
    {
        $this->delete("orgs/{$organizationSlug}/teams/{$teamId}/recipes/{$recipeId}");
    }

    /**
     * Get the collection of Forge recipes.
     *
     * @return \Laravel\Forge\Resources\ForgeRecipe[]
     */
    public function forgeRecipes()
    {
        return $this->transformCollection(
            $this->get('forge-recipes')['data'] ?? [],
            ForgeRecipe::class
        );
    }

    /**
     * Get a Forge recipe.
     *
     * @param  string  $forgeRecipeId
     * @return \Laravel\Forge\Resources\ForgeRecipe
     */
    public function forgeRecipe($forgeRecipeId)
    {
        return new ForgeRecipe($this->get("forge-recipes/{$forgeRecipeId}")['data'] ?? [], $this);
    }

    /**
     * Create a Forge recipe run.
     *
     * @param  string  $forgeRecipeId
     * @return \Laravel\Forge\Resources\RecipeRun
     */
    public function createForgeRecipeRun($forgeRecipeId, array $data)
    {
        $run = $this->post("forge-recipes/{$forgeRecipeId}/runs", $data)['data'] ?? [];

        return new RecipeRun($run + ['forge_recipe_id' => $forgeRecipeId], $this);
    }
}
