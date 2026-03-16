<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class PHPVersion extends Resource
{
    /**
     * The id of the PHP version.
     */
    public ?int $id = null;

    /**
     * The version of PHP.
     */
    public ?string $version = null;

    /**
     * The status of the version.
     */
    public ?string $status = null;

    /**
     * The binary name of PHP.
     */
    public ?string $binaryName = null;

    /**
     * The displayable version of PHP.
     */
    public ?string $displayableVersion = null;

    /**
     * Whether the version is used as the default when creating a new site.
     */
    public ?bool $usedAsDefault = null;

    /**
     * Whether the version is used on the CLI by default.
     */
    public ?bool $usedOnCli = null;

    /**
     * The date/time the PHP version was created.
     */
    public ?string $createdAt = null;

    /**
     * The date/time the PHP version was last updated.
     */
    public ?string $updatedAt = null;
}
