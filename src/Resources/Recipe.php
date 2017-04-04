<?php

namespace Themsaid\Forge\Resources;

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

    /**
     * Update the given recipe.
     *
     * @param  array $data
     * @return Recipe
     */
    public function update(array $data)
    {
        return $this->forge->updateRecipe($this->id, $data);
    }

    /**
     * Delete the given recipe.
     *
     * @return void
     */
    public function delete()
    {
        return $this->forge->deleteRecipe($this->id);
    }

    /**
     * Run the given recipe.
     *
     * @return void
     */
    public function run()
    {
        return $this->forge->runRecipe($this->id);
    }
}