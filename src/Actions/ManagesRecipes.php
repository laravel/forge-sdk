<?php

namespace Themsaid\Forge\Actions;

use Themsaid\Forge\Resources\Recipe;

trait ManagesRecipes
{
    /**
     * Get the collection of recipes.
     *
     * @return Recipe[]
     */
    public function recipes()
    {
        return $this->transformCollection(
            $this->get('recipes')['recipes'], Recipe::class
        );
    }

    /**
     * Get a recipe instance.
     *
     * @param  string $recipeId
     * @return Recipe
     */
    public function recipe($recipeId)
    {
        return new Recipe($this->get("recipes/$recipeId")['recipe']);
    }

    /**
     * Create a new recipe.
     *
     * @param  array $data
     * @return Recipe
     */
    public function createRecipe(array $data)
    {
        return new Recipe($this->post('recipes', $data)['recipe']);
    }

    /**
     * Update the given recipe.
     *
     * @param  string $recipeId
     * @param  array $data
     * @return Recipe
     */
    public function updateRecipe($recipeId, array $data)
    {
        return $this->put("recipes/$recipeId", $data)['recipe'];
    }

    /**
     * Delete the given recipe.
     *
     * @param  string $recipeId
     * @return void
     */
    public function deleteRecipe($recipeId)
    {
        $this->delete("recipes/$recipeId");
    }

    /**
     * Run the given recipe.
     *
     * @param  string $recipeId
     * @return void
     */
    public function runRecipe($recipeId)
    {
        $this->post("recipes/$recipeId/run");
    }
}