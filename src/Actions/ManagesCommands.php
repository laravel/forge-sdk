<?php

declare(strict_types=1);

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Command;

trait ManagesCommands
{
    /**
     * Get the collection of commands for a site.
     *
     * @return Command[]
     */
    public function commands(string $organizationSlug, int $serverId, int $siteId): array
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/commands")['data'] ?? [],
            Command::class,
            $organizationSlug,
            $serverId,
            $siteId,
        );
    }

    /**
     * Get a command instance.
     */
    public function command(string $organizationSlug, int $serverId, int $siteId, int $commandId): Command
    {
        return $this->newResource(
            Command::class,
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/commands/{$commandId}")['data'] ?? [],
            $organizationSlug,
            $serverId,
            $siteId,
        );
    }

    /**
     * Create a new command.
     */
    public function createCommand(string $organizationSlug, int $serverId, int $siteId, array $data): Command
    {
        return $this->newResource(
            Command::class,
            $this->post("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/commands", $data)['data'] ?? [],
            $organizationSlug,
            $serverId,
            $siteId,
        );
    }

    /**
     * Delete the given command.
     */
    public function deleteCommand(string $organizationSlug, int $serverId, int $siteId, int $commandId): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/commands/{$commandId}");
    }

    /**
     * Get the output for a command.
     */
    public function commandOutput(string $organizationSlug, int $serverId, int $siteId, int $commandId): string
    {
        $response = $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/commands/{$commandId}/output");

        return $response['data']['attributes']['output'] ?? '';
    }
}
