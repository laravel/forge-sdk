<?php

declare(strict_types=1);

namespace Tests\Integration;

use Laravel\Forge\Resources\User;

class UserTest extends IntegrationTestCase
{
    public function test_get_authenticated_user(): void
    {
        $user = $this->forge()->user();

        $this->assertInstanceOf(User::class, $user);
        $this->assertIsInt($user->id);
        $this->assertIsString($user->email);
        $this->assertNotEmpty($user->email);
        $this->assertIsString($user->name);
        $this->assertNotEmpty($user->name);
    }

    public function test_me_returns_same_user(): void
    {
        $user = $this->forge()->me();

        $this->assertInstanceOf(User::class, $user);
        $this->assertIsInt($user->id);
        $this->assertIsString($user->email);
    }

    public function test_user_has_no_jsonapi_envelope_keys(): void
    {
        $user = $this->forge()->user();

        $this->assertIsArray($user->relationships);
        $this->assertIsArray($user->links);
    }
}
