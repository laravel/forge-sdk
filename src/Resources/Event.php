<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class Event extends Resource
{
    /**
     * The id of the event.
     */
    public ?int $id = null;

    /**
     * The id of the server where the event occurred.
     */
    public ?int $serverId = null;

    /**
     * The user that ran the event.
     */
    public ?string $ranAs = null;

    /**
     * The name of the server the event occurred.
     */
    public ?string $serverName = null;

    /**
     * The description of the event.
     */
    public ?string $description = null;

    /**
     * The date/time the job was created.
     */
    public ?string $createdAt = null;

    /**
     * The date/time the event was last updated.
     */
    public ?string $updatedAt = null;
}
