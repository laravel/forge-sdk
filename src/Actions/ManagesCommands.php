<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Command;

trait ManagesCommands
{
    /**
     * Get the collection of commands for a site.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Command[]
     */
    public function commands($organizationId, $serverId, $siteId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/commands")['data'] ?? [],
            Command::class,
            ['organization_id' => $organizationId, 'server_id' => $serverId, 'site_id' => $siteId]
        );
    }

    /**
     * Get a command instance.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $commandId
     * @return \Laravel\Forge\Resources\Command
     */
    public function command($organizationId, $serverId, $siteId, $commandId)
    {
        return new Command(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/commands/{$commandId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a new command.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Command
     */
    public function createCommand($organizationId, $serverId, $siteId, array $data)
    {
        $command = $this->post(
            "orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/commands",
            $data
        )['data'] ?? [];

        return new Command(
            $command + ['organization_id' => $organizationId, 'server_id' => $serverId, 'site_id' => $siteId],
            $this
        );
    }

    /**
     * Delete the given command.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $commandId
     * @return void
     */
    public function deleteCommand($organizationId, $serverId, $siteId, $commandId)
    {
        $this->delete("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/commands/{$commandId}");
    }

    /**
     * Get the output for a command.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $commandId
     * @return string
     */
    public function commandOutput($organizationId, $serverId, $siteId, $commandId)
    {
        $response = $this->get("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/commands/{$commandId}/output");

        return $response['data']['output'] ?? $response['output'] ?? '';
    }
}
