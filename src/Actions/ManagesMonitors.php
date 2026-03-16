<?php

declare(strict_types=1);

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Monitor;

trait ManagesMonitors
{
    /**
     * Get the collection of monitors.
     *
     * @return Monitor[]
     */
    public function monitors(string $organizationSlug, int $serverId): array
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/monitors")['data'] ?? [],
            Monitor::class,
            $organizationSlug,
            $serverId,
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
    public function createMonitor(string $organizationSlug, int $serverId, array $data): Monitor
    {
        return $this->newResource(
            Monitor::class,
            $this->post("orgs/{$organizationSlug}/servers/{$serverId}/monitors", $data)['data'] ?? [],
            $organizationSlug,
            $serverId,
        );
    }

    /**
     * Delete the given monitor.
     */
    public function deleteMonitor(string $organizationSlug, int $serverId, int $monitorId): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/monitors/{$monitorId}");
    }
}
