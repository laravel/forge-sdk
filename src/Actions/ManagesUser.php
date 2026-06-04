<?php

declare(strict_types=1);

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\User;

trait ManagesUser
{
    /**
     * Get the authenticated user.
     */
    public function user(): User
    {
        return new User($this->get('user')['data'] ?? [], $this);
    }

    /**
     * Get the authenticated user (alias for user()).
     */
    public function me(): User
    {
        return new User($this->get('me')['data'] ?? [], $this);
    }
}
