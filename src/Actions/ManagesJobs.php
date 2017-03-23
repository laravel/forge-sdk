<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Job;

trait ManagesJobs
{
    /**
     * Get the collection of jobs.
     *
     * @param  integer $serverId
     * @return Job[]
     */
    public function jobs($serverId)
    {
        return $this->transformCollection(
            $this->get("servers/$serverId/jobs")['jobs'],
            Job::class,
            ['server_id' => $serverId]
        );
    }

    /**
     * Get a job instance.
     *
     * @param  integer $serverId
     * @param  integer $jobId
     * @return Job
     */
    public function job($serverId, $jobId)
    {
        return new Job(
            $this->get("servers/$serverId/jobs/$jobId")['job'] + ['server_id' => $serverId], $this
        );
    }

    /**
     * Create a new job.
     *
     * @param  integer $serverId
     * @param  array $data
     * @param  boolean $wait
     * @return Job
     */
    public function createJob($serverId, array $data, $wait = true)
    {
        $job = $this->post("servers/$serverId/jobs", $data)['job'];

        if ($wait) {
            return $this->retry(30, function () use ($serverId, $job) {
                $job = $this->job($serverId, $job['id']);

                return $job->status == 'installed' ? $job : null;
            });
        }

        return new Job($job + ['server_id' => $serverId], $this);
    }

    /**
     * Delete the given job.
     *
     * @param  integer $serverId
     * @param  integer $jobId
     * @return void
     */
    public function deleteJob($serverId, $jobId)
    {
        $this->delete("servers/$serverId/jobs/$jobId");
    }
}