<?php

declare(strict_types=1);

namespace Laravel\Forge\Actions;

use Laravel\Forge\CursorPaginator;
use Laravel\Forge\Resources\Monitor;

trait ManagesMonitors
{
    /**
     * Get the collection of monitors.
     */
    public function monitors(string $organizationSlug, int $serverId, array $query = []): CursorPaginator
    {
        return $this->paginatedCollection(
            "orgs/{$organizationSlug}/servers/{$serverId}/monitors",
            Monitor::class,
            $organizationSlug,
            $serverId,
            query: $query,
        );
    }

    /**
     * Get a monitor instance.
     */
    public function monitor(string $organizationSlug, int $serverId, int $monitorId): Monitor
    {
        return $this->newResource(
            Monitor::class,
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/monitors/{$monitorId}")['data'] ?? [],
            $organizationSlug,
            $serverId,
        );
    }

    /**
     * Create a new monitor.
     */
    public function createMonitor(string $organizationSlug, int $serverId, array $data): void
    {
        $this->post("orgs/{$organizationSlug}/servers/{$serverId}/monitors", $data);
    }

    /**
     * Delete the given monitor.
     */
    public function deleteMonitor(string $organizationSlug, int $serverId, int $monitorId): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/monitors/{$monitorId}");
    }
}
