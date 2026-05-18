<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class Heartbeat extends Resource
{
    /**
     * The id of the organization.
     */
    public ?string $organizationId = null;

    /**
     * The id of the heartbeat.
     */
    public ?int $id = null;

    /**
     * The id of the server.
     */
    public ?int $serverId = null;

    /**
     * The id of the site.
     */
    public ?int $siteId = null;

    /**
     * The name of the heartbeat.
     */
    public ?string $name = null;

    /**
     * The status of the heartbeat.
     */
    public ?string $status = null;

    /**
     * The grace period of the heartbeat (in minutes).
     */
    public ?int $gracePeriod = null;

    /**
     * The frequency of the heartbeat (in minutes).
     */
    public ?int $frequency = null;

    /**
     * The custom frequency of the heartbeat.
     */
    public ?string $customFrequency = null;

    /**
     * The ping URL of the heartbeat.
     */
    public ?string $pingUrl = null;

    /**
     * Delete the given heartbeat.
     */
    public function delete(): void
    {
        $this->forge->deleteHeartbeat($this->organizationId, $this->serverId, $this->siteId, $this->id);
    }
}
