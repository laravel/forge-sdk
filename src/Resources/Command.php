<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class Command extends Resource
{
    /**
     * The id of the command.
     */
    public ?int $id = null;

    /**
     * The id of the site.
     */
    public ?int $siteId = null;

    /**
     * The command.
     */
    public ?string $command = null;

    /**
     * The status of the command.
     */
    public ?string $status = null;

    /**
     * The output of the command.
     */
    public ?string $output = null;

    /**
     * The duration of the command.
     */
    public ?string $duration = null;

    /**
     * The ID of the user who ran the command.
     */
    public ?int $userId = null;

    /**
     * The date/time the command was created.
     */
    public ?string $createdAt = null;

    /**
     * The date/time the command was last updated.
     */
    public ?string $updatedAt = null;
}
