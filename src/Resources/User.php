<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class User extends Resource
{
    /**
     * The id of the user.
     */
    public ?int $id = null;

    /**
     * The name of the user.
     */
    public ?string $name = null;

    /**
     * The E-Mail of the user.
     */
    public ?string $email = null;

    /**
     * The date/time the user was created.
     */
    public ?string $createdAt = null;

    /**
     * The date/time the user was last updated.
     */
    public ?string $updatedAt = null;
}
