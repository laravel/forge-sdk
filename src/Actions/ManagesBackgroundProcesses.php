<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\BackgroundProcess;

trait ManagesBackgroundProcesses
{
    /**
     * Get the collection of background processes.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @return \Laravel\Forge\Resources\BackgroundProcess[]
     */
    public function backgroundProcesses($organizationId, $serverId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/background-processes")['data'] ?? [],
            BackgroundProcess::class,
            ['organization_id' => $organizationId, 'server_id' => $serverId]
        );
    }

    /**
     * Get a background process instance.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $processId
     * @return \Laravel\Forge\Resources\BackgroundProcess
     */
    public function backgroundProcess($organizationId, $serverId, $processId)
    {
        return new BackgroundProcess(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/background-processes/{$processId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a new background process.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @return \Laravel\Forge\Resources\BackgroundProcess
     */
    public function createBackgroundProcess($organizationId, $serverId, array $data)
    {
        $process = $this->post("orgs/{$organizationId}/servers/{$serverId}/background-processes", $data)['data'] ?? [];

        return new BackgroundProcess($process + ['organization_id' => $organizationId, 'server_id' => $serverId], $this);
    }

    /**
     * Update a background process.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $processId
     * @return \Laravel\Forge\Resources\BackgroundProcess
     */
    public function updateBackgroundProcess($organizationId, $serverId, $processId, array $data)
    {
        $process = $this->put(
            "orgs/{$organizationId}/servers/{$serverId}/background-processes/{$processId}",
            $data
        )['data'] ?? [];

        return new BackgroundProcess($process, $this);
    }

    /**
     * Delete the given background process.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $processId
     * @return void
     */
    public function deleteBackgroundProcess($organizationId, $serverId, $processId)
    {
        $this->delete("orgs/{$organizationId}/servers/{$serverId}/background-processes/{$processId}");
    }

    /**
     * Get the log for a background process.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $processId
     * @return string
     */
    public function backgroundProcessLog($organizationId, $serverId, $processId)
    {
        $response = $this->get("orgs/{$organizationId}/servers/{$serverId}/background-processes/{$processId}/log");

        return $response['data']['log'] ?? $response['log'] ?? '';
    }
}
