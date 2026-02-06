<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\BackgroundProcess;

trait ManagesBackgroundProcesses
{
    /**
     * Get the collection of background processes.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return \Laravel\Forge\Resources\BackgroundProcess[]
     */
    public function backgroundProcesses($organizationSlug, $serverId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/background-processes")['data'] ?? [],
            BackgroundProcess::class,
            ['organization_id' => $organizationSlug, 'server_id' => $serverId]
        );
    }

    /**
     * Get a background process instance.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $processId
     * @return \Laravel\Forge\Resources\BackgroundProcess
     */
    public function backgroundProcess($organizationSlug, $serverId, $processId)
    {
        return new BackgroundProcess(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/background-processes/{$processId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a new background process.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return \Laravel\Forge\Resources\BackgroundProcess
     */
    public function createBackgroundProcess($organizationSlug, $serverId, array $data)
    {
        $process = $this->post("orgs/{$organizationSlug}/servers/{$serverId}/background-processes", $data)['data'] ?? [];

        return new BackgroundProcess($process + ['organization_id' => $organizationSlug, 'server_id' => $serverId], $this);
    }

    /**
     * Update a background process.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $processId
     * @return \Laravel\Forge\Resources\BackgroundProcess
     */
    public function updateBackgroundProcess($organizationSlug, $serverId, $processId, array $data)
    {
        $process = $this->put(
            "orgs/{$organizationSlug}/servers/{$serverId}/background-processes/{$processId}",
            $data
        )['data'] ?? [];

        return new BackgroundProcess($process, $this);
    }

    /**
     * Delete the given background process.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $processId
     * @return void
     */
    public function deleteBackgroundProcess($organizationSlug, $serverId, $processId)
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/background-processes/{$processId}");
    }

    /**
     * Get the log for a background process.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $processId
     * @return string
     */
    public function backgroundProcessLog($organizationSlug, $serverId, $processId)
    {
        $response = $this->get("orgs/{$organizationSlug}/servers/{$serverId}/background-processes/{$processId}/log");

        return $response['data']['log'] ?? $response['log'] ?? '';
    }

    /**
     * Perform an action on a background process.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $backgroundProcessId
     * @return array
     */
    public function performBackgroundProcessAction($organizationSlug, $serverId, $backgroundProcessId, array $data)
    {
        return $this->post("orgs/{$organizationSlug}/servers/{$serverId}/background-processes/{$backgroundProcessId}/actions", $data);
    }
}
