<?php

declare(strict_types=1);

namespace Tests\Integration;

use Laravel\Forge\Resources\ScheduledJob;

class ScheduledJobsTest extends IntegrationTestCase
{
    public function test_list_scheduled_jobs(): void
    {
        $jobs = $this->forge()->scheduledJobs($this->organization(), $this->serverId());

        $this->assertIsArray($jobs);

        if (count($jobs) === 0) {
            $this->markTestSkipped('No scheduled jobs found on the test server.');
        }

        $job = $jobs[0];
        $this->assertInstanceOf(ScheduledJob::class, $job);
        $this->assertIsInt($job->id);
        $this->assertIsString($job->command);
        $this->assertNotEmpty($job->command);
        $this->assertIsString($job->status);
        $this->assertIsString($job->createdAt, 'createdAt should be hydrated');

        // v2 properties
        $this->assertTrue(
            is_null($job->name) || is_string($job->name),
            'name should be null or string'
        );
        $this->assertTrue(
            is_null($job->frequency) || is_string($job->frequency),
            'frequency should be null or string'
        );
        $this->assertTrue(
            is_null($job->cron) || is_string($job->cron),
            'cron should be null or string'
        );
        $this->assertTrue(
            is_null($job->updatedAt) || is_string($job->updatedAt),
            'updatedAt should be null or string'
        );
    }

    public function test_scheduled_job_has_no_jsonapi_envelope_keys(): void
    {
        $jobs = $this->forge()->scheduledJobs($this->organization(), $this->serverId());

        if (count($jobs) === 0) {
            $this->markTestSkipped('No scheduled jobs found on the test server.');
        }

        $job = $jobs[0];
        $this->assertArrayNotHasKey('relationships', $job->attributes);
        $this->assertArrayNotHasKey('links', $job->attributes);
    }
}
