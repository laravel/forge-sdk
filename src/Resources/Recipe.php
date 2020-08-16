<?php

namespace Laravel\Forge\Resources;

class Recipe extends Resource
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
     * @param  array  $data
     * @return \Laravel\Forge\Resources\Recipe
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
        $this->forge->deleteRecipe($this->id);
    }

    /**
     * Run the given recipe.
     *
     * @param  array  $data
     * @return void
     */
    public function run(array $data)
    {
        $this->forge->runRecipe($this->id, $data);
    }
}
