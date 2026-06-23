<?php

declare(strict_types=1);

namespace Laravel\Forge\Actions;

trait ManagesLogs
{
    /**
     * Get server log content.
     */
    public function serverLog(string $organizationSlug, int $serverId, string $logKey): string
    {
        $response = $this->get("v1/servers/{$serverId}/logs", ['file' => $logKey]);

        return $response['content'] ?? '';
    }

    /**
     * Delete server log content.
     */
    public function deleteServerLog(string $organizationSlug, int $serverId, string $logKey): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/logs/{$logKey}");
    }
}
