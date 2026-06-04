<?php

declare(strict_types=1);

namespace Tests\Integration;

use Laravel\Forge\CursorPaginator;
use Laravel\Forge\Resources\Recipe;

class RecipesTest extends IntegrationTestCase
{
    public function test_list_recipes(): void
    {
        $recipes = $this->forge()->recipes($this->organization());

        $this->assertInstanceOf(CursorPaginator::class, $recipes);
    }

    public function test_crud_recipe(): void
    {
        $org = $this->organization();
        $suffix = time();

        // Create
        $recipe = $this->forge()->createRecipe($org, [
            'name' => "SDK Test Recipe {$suffix}",
            'user' => 'root',
            'script' => 'echo "Hello from SDK integration test"',
        ]);

        $this->assertInstanceOf(Recipe::class, $recipe);
        $this->assertIsInt($recipe->id);
        $this->assertSame("SDK Test Recipe {$suffix}", $recipe->name);
        $this->assertSame('root', $recipe->user);
        $this->assertIsString($recipe->createdAt, 'createdAt should be hydrated on create');

        try {
            usleep(500_000);

            // Read
            $fetched = $this->forge()->recipe($org, $recipe->id);
            $this->assertSame($recipe->id, $fetched->id);
            $this->assertSame("SDK Test Recipe {$suffix}", $fetched->name);
            $this->assertSame('root', $fetched->user);

            // v2 properties on read
            $this->assertTrue(
                is_null($fetched->script) || is_string($fetched->script),
                'script should be null or string'
            );
            $this->assertTrue(
                is_null($fetched->updatedAt) || is_string($fetched->updatedAt),
                'updatedAt should be null or string'
            );

            // Envelope keys stripped
            $this->assertIsArray($fetched->relationships);
            $this->assertIsArray($fetched->links);

            usleep(500_000);

            // Update
            $updated = $this->forge()->updateRecipe($org, $recipe->id, [
                'name' => "SDK Test Recipe {$suffix} Updated",
                'user' => 'forge',
                'script' => 'echo "Updated"',
            ]);

            $this->assertSame("SDK Test Recipe {$suffix} Updated", $updated->name);
        } finally {
            usleep(500_000);

            // Delete (always clean up)
            $this->forge()->deleteRecipe($org, $recipe->id);
        }
    }
}
