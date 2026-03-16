<?php

declare(strict_types=1);

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\BackgroundProcess;

trait ManagesBackgroundProcesses
{
    /**
     * Get the collection of background processes.
     *
     * @return BackgroundProcess[]
     */
    public function backgroundProcesses(string $organizationSlug, int $serverId): array
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/background-processes")['data'] ?? [],
            BackgroundProcess::class,
            $organizationSlug,
            $serverId,
        );
    }

    /**
     * Get a background process instance.
     */
    public function backgroundProcess(string $organizationSlug, int $serverId, int $processId): BackgroundProcess
    {
        return $this->newResource(
            BackgroundProcess::class,
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/background-processes/{$processId}")['data'] ?? [],
            $organizationSlug,
            $serverId,
        );
    }

    /**
     * Create a new background process.
     */
    public function createBackgroundProcess(string $organizationSlug, int $serverId, array $data): BackgroundProcess
    {
        return $this->newResource(
            BackgroundProcess::class,
            $this->post("orgs/{$organizationSlug}/servers/{$serverId}/background-processes", $data)['data'] ?? [],
            $organizationSlug,
            $serverId,
        );
    }

    /**
     * Update a background process.
     */
    public function updateBackgroundProcess(string $organizationSlug, int $serverId, int $processId, array $data): BackgroundProcess
    {
        return $this->newResource(
            BackgroundProcess::class,
            $this->put("orgs/{$organizationSlug}/servers/{$serverId}/background-processes/{$processId}", $data)['data'] ?? [],
            $organizationSlug,
            $serverId,
        );
    }

    /**
     * Delete the given background process.
     */
    public function deleteBackgroundProcess(string $organizationSlug, int $serverId, int $processId): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/background-processes/{$processId}");
    }

    /**
     * Get the log for a background process.
     */
    public function backgroundProcessLog(string $organizationSlug, int $serverId, int $processId): string
    {
        $response = $this->get("orgs/{$organizationSlug}/servers/{$serverId}/background-processes/{$processId}/log");

        return $response['data']['log'] ?? $response['log'] ?? '';
    }

    /**
     * Perform an action on a background process.
     */
    public function performBackgroundProcessAction(string $organizationSlug, int $serverId, int $backgroundProcessId, array $data): array
    {
        return $this->post("orgs/{$organizationSlug}/servers/{$serverId}/background-processes/{$backgroundProcessId}/actions", $data);
    }
}
