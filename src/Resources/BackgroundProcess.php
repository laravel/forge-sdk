<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class BackgroundProcess extends Resource
{
    /**
     * The id of the background process.
     */
    public ?int $id = null;

    /**
     * The id of the server.
     */
    public ?int $serverId = null;

    /**
     * The command of the background process.
     */
    public ?string $command = null;

    /**
     * The user running the background process.
     */
    public ?string $user = null;

    /**
     * The directory of the background process.
     */
    public ?string $directory = null;

    /**
     * The number of processes.
     */
    public ?int $processes = null;

    /**
     * The status of the background process.
     */
    public ?string $status = null;

    /**
     * The date/time the background process was created.
     */
    public ?string $createdAt = null;
}
