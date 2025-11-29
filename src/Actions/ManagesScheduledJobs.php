<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\ScheduledJob;

trait ManagesScheduledJobs
{
    /**
     * Get the collection of scheduled jobs.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @return \Laravel\Forge\Resources\ScheduledJob[]
     */
    public function scheduledJobs($organizationId, $serverId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/scheduled-jobs")['data'] ?? [],
            ScheduledJob::class,
            ['organization_id' => $organizationId, 'server_id' => $serverId]
        );
    }

    /**
     * Get a scheduled job instance.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $jobId
     * @return \Laravel\Forge\Resources\ScheduledJob
     */
    public function scheduledJob($organizationId, $serverId, $jobId)
    {
        return new ScheduledJob(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/scheduled-jobs/{$jobId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a new scheduled job.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  array  $data
     * @return \Laravel\Forge\Resources\ScheduledJob
     */
    public function createScheduledJob($organizationId, $serverId, array $data)
    {
        $job = $this->post("orgs/{$organizationId}/servers/{$serverId}/scheduled-jobs", $data)['data'] ?? [];

        return new ScheduledJob($job + ['organization_id' => $organizationId, 'server_id' => $serverId], $this);
    }

    /**
     * Delete the given scheduled job.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $jobId
     * @return void
     */
    public function deleteScheduledJob($organizationId, $serverId, $jobId)
    {
        $this->delete("orgs/{$organizationId}/servers/{$serverId}/scheduled-jobs/{$jobId}");
    }

    /**
     * Get the output for a scheduled job.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $jobId
     * @return string
     */
    public function scheduledJobOutput($organizationId, $serverId, $jobId)
    {
        $response = $this->get("orgs/{$organizationId}/servers/{$serverId}/scheduled-jobs/{$jobId}/output");

        return $response['data']['output'] ?? $response['output'] ?? '';
    }
}
