<?php

declare(strict_types=1);

namespace Laravel\Forge\Actions;

use Laravel\Forge\Enums\LogKey;

trait ManagesLogs
{
    /**
     * Get server log content.
     */
    public function serverLog(string $organizationSlug, int $serverId, LogKey|string $logKey): string
    {
        $key = $logKey instanceof LogKey ? $logKey->value : $logKey;

        $response = $this->get("orgs/{$organizationSlug}/servers/{$serverId}/logs/{$key}");

        return $response['data']['attributes']['content'] ?? '';
    }

    /**
     * Delete server log content.
     */
    public function deleteServerLog(string $organizationSlug, int $serverId, LogKey|string $logKey): void
    {
        $key = $logKey instanceof LogKey ? $logKey->value : $logKey;

        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/logs/{$key}");
    }
}
