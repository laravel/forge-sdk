<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class Recipe extends Resource
{
    /**
     * The id of the recipe.
     */
    public ?int $id = null;

    /**
     * The id of the organization.
     */
    public ?string $organizationId = null;

    /**
     * The id of the team (when fetched via the team-shared endpoint).
     */
    public ?int $teamId = null;

    /**
     * The name of the recipe.
     */
    public ?string $name = null;

    /**
     * The user that runs the recipe on the server.
     */
    public ?string $user = null;

    /**
     * The script content of the recipe.
     */
    public ?string $script = null;

    /**
     * The date/time the recipe was created.
     */
    public ?string $createdAt = null;

    /**
     * The date/time the recipe was last updated.
     */
    public ?string $updatedAt = null;

    /**
     * Update the given recipe.
     */
    public function update(array $data): Recipe
    {
        return $this->forge->updateRecipe($this->organizationId, $this->id, $data);
    }

    /**
     * Delete the given recipe.
     */
    public function delete(): void
    {
        $this->forge->deleteRecipe($this->organizationId, $this->id);
    }

    /**
     * Run the given recipe.
     */
    public function run(array $data): void
    {
        $this->forge->createRecipeRun($this->organizationId, $this->id, $data);
    }
}
