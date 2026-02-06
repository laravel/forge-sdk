<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\ScheduledJob;

trait ManagesScheduledJobs
{
    /**
     * Get the collection of scheduled jobs.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return \Laravel\Forge\Resources\ScheduledJob[]
     */
    public function scheduledJobs($organizationSlug, $serverId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/scheduled-jobs")['data'] ?? [],
            ScheduledJob::class,
            ['organization_id' => $organizationSlug, 'server_id' => $serverId]
        );
    }

    /**
     * Get a scheduled job instance.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $jobId
     * @return \Laravel\Forge\Resources\ScheduledJob
     */
    public function scheduledJob($organizationSlug, $serverId, $jobId)
    {
        return new ScheduledJob(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/scheduled-jobs/{$jobId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a new scheduled job.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return \Laravel\Forge\Resources\ScheduledJob
     */
    public function createScheduledJob($organizationSlug, $serverId, array $data)
    {
        $job = $this->post("orgs/{$organizationSlug}/servers/{$serverId}/scheduled-jobs", $data)['data'] ?? [];

        return new ScheduledJob($job + ['organization_id' => $organizationSlug, 'server_id' => $serverId], $this);
    }

    /**
     * Delete the given scheduled job.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $jobId
     * @return void
     */
    public function deleteScheduledJob($organizationSlug, $serverId, $jobId)
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/scheduled-jobs/{$jobId}");
    }

    /**
     * Get the output for a scheduled job.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $jobId
     * @return string
     */
    public function scheduledJobOutput($organizationSlug, $serverId, $jobId)
    {
        $response = $this->get("orgs/{$organizationSlug}/servers/{$serverId}/scheduled-jobs/{$jobId}/output");

        return $response['data']['output'] ?? $response['output'] ?? '';
    }

    /**
     * Get the collection of scheduled jobs for a site.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\ScheduledJob[]
     */
    public function siteScheduledJobs($organizationSlug, $serverId, $siteId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/scheduled-jobs")['data'] ?? [],
            ScheduledJob::class,
            ['organization_id' => $organizationSlug, 'server_id' => $serverId, 'site_id' => $siteId]
        );
    }

    /**
     * Get a scheduled job for a site.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $jobId
     * @return \Laravel\Forge\Resources\ScheduledJob
     */
    public function siteScheduledJob($organizationSlug, $serverId, $siteId, $jobId)
    {
        return new ScheduledJob(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/scheduled-jobs/{$jobId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a new scheduled job for a site.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\ScheduledJob
     */
    public function createSiteScheduledJob($organizationSlug, $serverId, $siteId, array $data)
    {
        $job = $this->post("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/scheduled-jobs", $data)['data'] ?? [];

        return new ScheduledJob($job + ['organization_id' => $organizationSlug, 'server_id' => $serverId, 'site_id' => $siteId], $this);
    }

    /**
     * Delete a scheduled job for a site.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $jobId
     * @return void
     */
    public function deleteSiteScheduledJob($organizationSlug, $serverId, $siteId, $jobId)
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/scheduled-jobs/{$jobId}");
    }

    /**
     * Get the output for a scheduled job for a site.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $jobId
     * @return string
     */
    public function siteScheduledJobOutput($organizationSlug, $serverId, $siteId, $jobId)
    {
        $response = $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/scheduled-jobs/{$jobId}/output");

        return $response['data']['output'] ?? $response['output'] ?? '';
    }
}
