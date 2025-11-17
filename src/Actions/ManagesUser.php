<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\User;

trait ManagesUser
{
    /**
     * Get the authenticated user.
     *
     * @return \Laravel\Forge\Resources\User
     */
    public function user()
    {
        return new User($this->get('user')['data'] ?? [], $this);
    }

    /**
     * Get the authenticated user (alias for user()).
     *
     * @return \Laravel\Forge\Resources\User
     */
    public function me()
    {
        return new User($this->get('me')['data'] ?? [], $this);
    }
}
