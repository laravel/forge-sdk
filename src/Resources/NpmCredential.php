<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class NpmCredential extends Resource
{
    /**
     * The id of the npm credential.
     */
    public ?string $id = null;

    /**
     * The registry the credential applies to.
     */
    public ?string $registry = null;

    /**
     * The scopes the credential applies to.
     */
    public array $scopes = [];

    /**
     * The token used for authentication.
     */
    public ?string $token = null;
}
