<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class Monitor extends Resource
{
    /**
     * The id of the organization.
     */
    public ?string $organizationId = null;

    /**
     * The id of the monitor.
     */
    public ?int $id = null;

    /**
     * The id of the server.
     */
    public ?int $serverId = null;

    /**
     * The status of the monitor.
     */
    public ?string $status = null;

    /**
     * The type of the monitor.
     */
    public ?string $type = null;

    /**
     * The comparison operator of the monitor.
     */
    public ?string $operator = null;

    /**
     * The threshold of the monitor.
     */
    public ?float $threshold = null;

    /**
     * The minutes of the monitor.
     */
    public ?int $minutes = null;

    /**
     * The state of the monitor.
     */
    public ?string $state = null;

    /**
     * The state date/time of the monitor.
     */
    public ?string $stateChangedAt = null;

    /**
     * The notify setting of the monitor.
     */
    public ?string $notify = null;

    /**
     * The date/time the monitor was created.
     */
    public ?string $createdAt = null;

    /**
     * The date/time the monitor was last updated.
     */
    public ?string $updatedAt = null;
}
