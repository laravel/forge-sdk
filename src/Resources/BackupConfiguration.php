<?php

namespace Laravel\Forge\Resources;

class BackupConfiguration extends Resource
{
    /**
     * The id of the backup configuration.
     *
     * @var int
     */
    public $id;

    /**
     * The id of the organization.
     *
     * @var string
     */
    public $organizationId;

    /**
     * The id of the server.
     *
     * @var int
     */
    public $serverId;

    /**
     * The name of the backup configuration.
     *
     * @var string
     */
    public $name;

    /**
     * The storage provider id.
     *
     * @var int
     */
    public $storageProviderId;

    /**
     * The provider name.
     *
     * @var string
     */
    public $provider;

    /**
     * The bucket name.
     *
     * @var string
     */
    public $bucket;

    /**
     * The directory path.
     *
     * @var string
     */
    public $directory;

    /**
     * The schedule configuration.
     *
     * @var array
     */
    public $schedule;

    /**
     * The displayable schedule string.
     *
     * @var string
     */
    public $displayableSchedule;

    /**
     * The next run time.
     *
     * @var string
     */
    public $nextRunTime;

    /**
     * The status of the backup configuration.
     *
     * @var string
     */
    public $status;

    /**
     * The day of week.
     *
     * @var int|null
     */
    public $dayOfWeek;

    /**
     * The time.
     *
     * @var string
     */
    public $time;

    /**
     * The cron schedule.
     *
     * @var string
     */
    public $cronSchedule;

    /**
     * The retention period.
     *
     * @var int
     */
    public $retention;

    /**
     * The notification email.
     *
     * @var string
     */
    public $notifyEmail;

    /**
     * Update the backup configuration.
     *
     * @return void
     */
    public function update(array $data)
    {
        $this->forge->updateBackupConfiguration(
            $this->organizationId,
            $this->serverId,
            $this->id,
            $data
        );
    }

    /**
     * Delete the backup configuration.
     *
     * @return void
     */
    public function delete()
    {
        $this->forge->deleteBackupConfiguration(
            $this->organizationId,
            $this->serverId,
            $this->id
        );
    }

    /**
     * Get backups for this configuration.
     *
     * @return \Laravel\Forge\Resources\Backup[]
     */
    public function backups()
    {
        return $this->forge->backups(
            $this->organizationId,
            $this->serverId,
            $this->id
        );
    }

    /**
     * Create a new backup.
     *
     * @return void
     */
    public function createBackup()
    {
        $this->forge->createBackup(
            $this->organizationId,
            $this->serverId,
            $this->id
        );
    }
}
