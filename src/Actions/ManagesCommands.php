<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Command;

trait ManagesCommands
{
    /**
     * Get the collection of commands for a site.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Command[]
     */
    public function commands($organizationSlug, $serverId, $siteId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/commands")['data'] ?? [],
            Command::class,
            ['organization_id' => $organizationSlug, 'server_id' => $serverId, 'site_id' => $siteId]
        );
    }

    /**
     * Get a command instance.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $commandId
     * @return \Laravel\Forge\Resources\Command
     */
    public function command($organizationSlug, $serverId, $siteId, $commandId)
    {
        return new Command(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/commands/{$commandId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a new command.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Command
     */
    public function createCommand($organizationSlug, $serverId, $siteId, array $data)
    {
        $command = $this->post(
            "orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/commands",
            $data
        )['data'] ?? [];

        return new Command(
            $command + ['organization_id' => $organizationSlug, 'server_id' => $serverId, 'site_id' => $siteId],
            $this
        );
    }

    /**
     * Delete the given command.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $commandId
     * @return void
     */
    public function deleteCommand($organizationSlug, $serverId, $siteId, $commandId)
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/commands/{$commandId}");
    }

    /**
     * Get the output for a command.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $commandId
     * @return string
     */
    public function commandOutput($organizationSlug, $serverId, $siteId, $commandId)
    {
        $response = $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/commands/{$commandId}/output");

        return $response['data']['output'] ?? $response['output'] ?? '';
    }
}
