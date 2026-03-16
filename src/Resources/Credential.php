<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class Credential extends Resource
{
    /**
     * The id of the credential.
     */
    public ?int $id = null;

    /**
     * The name of the credential.
     */
    public ?string $name = null;

    /**
     * The type of the credential.
     */
    public ?string $type = null;
}
