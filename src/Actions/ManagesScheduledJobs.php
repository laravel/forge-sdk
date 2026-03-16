<?php

declare(strict_types=1);

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\ScheduledJob;

trait ManagesScheduledJobs
{
    /**
     * Get the collection of scheduled jobs.
     *
     * @return ScheduledJob[]
     */
    public function scheduledJobs(string $organizationSlug, int $serverId): array
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/scheduled-jobs")['data'] ?? [],
            ScheduledJob::class,
            $organizationSlug,
            $serverId,
        );
    }

    /**
     * Get a scheduled job instance.
     */
    public function scheduledJob(string $organizationSlug, int $serverId, int $jobId): ScheduledJob
    {
        return $this->newResource(
            ScheduledJob::class,
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/scheduled-jobs/{$jobId}")['data'] ?? [],
            $organizationSlug,
            $serverId,
        );
    }

    /**
     * Create a new scheduled job.
     */
    public function createScheduledJob(string $organizationSlug, int $serverId, array $data): ScheduledJob
    {
        return $this->newResource(
            ScheduledJob::class,
            $this->post("orgs/{$organizationSlug}/servers/{$serverId}/scheduled-jobs", $data)['data'] ?? [],
            $organizationSlug,
            $serverId,
        );
    }

    /**
     * Delete the given scheduled job.
     */
    public function deleteScheduledJob(string $organizationSlug, int $serverId, int $jobId): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/scheduled-jobs/{$jobId}");
    }

    /**
     * Get the output for a scheduled job.
     */
    public function scheduledJobOutput(string $organizationSlug, int $serverId, int $jobId): string
    {
        $response = $this->get("orgs/{$organizationSlug}/servers/{$serverId}/scheduled-jobs/{$jobId}/output");

        return $response['data']['output'] ?? $response['output'] ?? '';
    }

    /**
     * Get the collection of scheduled jobs for a site.
     *
     * @return ScheduledJob[]
     */
    public function siteScheduledJobs(string $organizationSlug, int $serverId, int $siteId): array
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/scheduled-jobs")['data'] ?? [],
            ScheduledJob::class,
            $organizationSlug,
            $serverId,
            $siteId,
        );
    }

    /**
     * Get a scheduled job for a site.
     */
    public function siteScheduledJob(string $organizationSlug, int $serverId, int $siteId, int $jobId): ScheduledJob
    {
        return $this->newResource(
            ScheduledJob::class,
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/scheduled-jobs/{$jobId}")['data'] ?? [],
            $organizationSlug,
            $serverId,
            $siteId,
        );
    }

    /**
     * Create a new scheduled job for a site.
     */
    public function createSiteScheduledJob(string $organizationSlug, int $serverId, int $siteId, array $data): ScheduledJob
    {
        return $this->newResource(
            ScheduledJob::class,
            $this->post("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/scheduled-jobs", $data)['data'] ?? [],
            $organizationSlug,
            $serverId,
            $siteId,
        );
    }

    /**
     * Delete a scheduled job for a site.
     */
    public function deleteSiteScheduledJob(string $organizationSlug, int $serverId, int $siteId, int $jobId): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/scheduled-jobs/{$jobId}");
    }

    /**
     * Get the output for a scheduled job for a site.
     */
    public function siteScheduledJobOutput(string $organizationSlug, int $serverId, int $siteId, int $jobId): string
    {
        $response = $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/scheduled-jobs/{$jobId}/output");

        return $response['data']['output'] ?? $response['output'] ?? '';
    }
}
