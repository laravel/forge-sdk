<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class PredefinedRole extends Resource
{
    /**
     * The id of the predefined role.
     */
    public ?int $id = null;

    /**
     * The name of the predefined role.
     */
    public ?string $name = null;

    /**
     * The date/time the predefined role was created.
     */
    public ?string $createdAt = null;

    /**
     * The date/time the predefined role was last updated.
     */
    public ?string $updatedAt = null;
}
