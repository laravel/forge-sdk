<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class ComposerCredential extends Resource
{
    /**
     * The id of the composer credential.
     */
    public ?string $id = null;

    /**
     * The repository the credential applies to.
     */
    public ?string $repository = null;

    /**
     * The username used for authentication.
     */
    public ?string $username = null;

    /**
     * The password used for authentication.
     */
    public ?string $password = null;
}
