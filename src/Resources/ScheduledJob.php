<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class ScheduledJob extends Resource
{
    /**
     * The id of the scheduled job.
     */
    public ?int $id = null;

    /**
     * The id of the server.
     */
    public ?int $serverId = null;

    /**
     * The command of the scheduled job.
     */
    public ?string $command = null;

    /**
     * The user running the scheduled job.
     */
    public ?string $user = null;

    /**
     * The frequency of the scheduled job.
     */
    public ?string $frequency = null;

    /**
     * The status of the scheduled job.
     */
    public ?string $status = null;

    /**
     * The name of the scheduled job.
     */
    public ?string $name = null;

    /**
     * The cron expression of the scheduled job.
     */
    public ?string $cron = null;

    /**
     * The next run time of the scheduled job.
     */
    public ?string $nextRunTime = null;

    /**
     * The date/time the scheduled job was created.
     */
    public ?string $createdAt = null;

    /**
     * The date/time the scheduled job was last updated.
     */
    public ?string $updatedAt = null;
}
