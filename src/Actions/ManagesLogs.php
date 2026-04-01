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
        $response = $this->get("orgs/{$organizationSlug}/servers/{$serverId}/logs/{$logKey}");

        return $response['data']['attributes']['content'] ?? '';
    }

    /**
     * Delete server log content.
     */
    public function deleteServerLog(string $organizationSlug, int $serverId, string $logKey): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/logs/{$logKey}");
    }
}
