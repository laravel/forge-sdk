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
     * @param  string  $organizationId
     * @return \Laravel\Forge\Resources\Recipe[]
     */
    public function recipes($organizationId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationId}/recipes")['data'] ?? [],
            Recipe::class,
            ['organization_id' => $organizationId]
        );
    }

    /**
     * Get a recipe.
     *
     * @param  string  $organizationId
     * @param  string  $recipeId
     * @return \Laravel\Forge\Resources\Recipe
     */
    public function recipe($organizationId, $recipeId)
    {
        return new Recipe($this->get("orgs/{$organizationId}/recipes/{$recipeId}")['data'] ?? [], $this);
    }

    /**
     * Create a new recipe.
     *
     * @param  string  $organizationId
     * @return \Laravel\Forge\Resources\Recipe
     */
    public function createRecipe($organizationId, array $data)
    {
        $recipe = $this->post("orgs/{$organizationId}/recipes", $data)['data'] ?? [];

        return new Recipe($recipe + ['organization_id' => $organizationId], $this);
    }

    /**
     * Update a recipe.
     *
     * @param  string  $organizationId
     * @param  string  $recipeId
     * @return \Laravel\Forge\Resources\Recipe
     */
    public function updateRecipe($organizationId, $recipeId, array $data)
    {
        $recipe = $this->put("orgs/{$organizationId}/recipes/{$recipeId}", $data)['data'] ?? [];

        return new Recipe($recipe, $this);
    }

    /**
     * Delete a recipe.
     *
     * @param  string  $organizationId
     * @param  string  $recipeId
     * @return void
     */
    public function deleteRecipe($organizationId, $recipeId)
    {
        $this->delete("orgs/{$organizationId}/recipes/{$recipeId}");
    }

    /**
     * Get the collection of recipe runs for a recipe.
     *
     * @param  string  $organizationId
     * @param  string  $recipeId
     * @return \Laravel\Forge\Resources\RecipeRun[]
     */
    public function recipeRuns($organizationId, $recipeId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationId}/recipes/{$recipeId}/runs")['data'] ?? [],
            RecipeRun::class,
            ['organization_id' => $organizationId, 'recipe_id' => $recipeId]
        );
    }

    /**
     * Get a recipe run.
     *
     * @param  string  $organizationId
     * @param  string  $recipeId
     * @param  string  $logId
     * @return \Laravel\Forge\Resources\RecipeRun
     */
    public function recipeRun($organizationId, $recipeId, $logId)
    {
        return new RecipeRun(
            $this->get("orgs/{$organizationId}/recipes/{$recipeId}/runs/{$logId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a new recipe run.
     *
     * @param  string  $organizationId
     * @param  string  $recipeId
     * @return \Laravel\Forge\Resources\RecipeRun
     */
    public function createRecipeRun($organizationId, $recipeId, array $data)
    {
        $run = $this->post("orgs/{$organizationId}/recipes/{$recipeId}/runs", $data)['data'] ?? [];

        return new RecipeRun($run + ['organization_id' => $organizationId, 'recipe_id' => $recipeId], $this);
    }

    /**
     * Get the collection of recipes for a team.
     *
     * @param  string  $organizationId
     * @param  string  $teamId
     * @return \Laravel\Forge\Resources\Recipe[]
     */
    public function teamRecipes($organizationId, $teamId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationId}/teams/{$teamId}/recipes")['data'] ?? [],
            Recipe::class,
            ['organization_id' => $organizationId, 'team_id' => $teamId]
        );
    }

    /**
     * Share a recipe with a team (create team recipes share).
     *
     * @param  string  $organizationId
     * @param  string  $teamId
     * @return \Laravel\Forge\Resources\Recipe
     */
    public function createTeamRecipesShare($organizationId, $teamId, array $data)
    {
        $recipe = $this->post("orgs/{$organizationId}/teams/{$teamId}/recipes", $data)['data'] ?? [];

        return new Recipe($recipe + ['organization_id' => $organizationId, 'team_id' => $teamId], $this);
    }

    /**
     * Remove a recipe share from a team (delete team recipes share).
     *
     * @param  string  $organizationId
     * @param  string  $teamId
     * @param  string  $recipeId
     * @return void
     */
    public function deleteTeamRecipesShare($organizationId, $teamId, $recipeId)
    {
        $this->delete("orgs/{$organizationId}/teams/{$teamId}/recipes/{$recipeId}");
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
