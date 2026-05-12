<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

use Laravel\Forge\CursorPaginator;

class BackupConfiguration extends Resource
{
    /**
     * The id of the backup configuration.
     */
    public ?int $id = null;

    /**
     * The id of the organization.
     */
    public ?string $organizationId = null;

    /**
     * The id of the server.
     */
    public ?int $serverId = null;

    /**
     * The name of the backup configuration.
     */
    public ?string $name = null;

    /**
     * The storage provider id.
     */
    public ?int $storageProviderId = null;

    /**
     * The provider name.
     */
    public ?string $provider = null;

    /**
     * The bucket name.
     */
    public ?string $bucket = null;

    /**
     * The directory path.
     */
    public ?string $directory = null;

    /**
     * The schedule configuration.
     */
    public ?string $schedule = null;

    /**
     * The database IDs covered by this backup configuration.
     *
     * @var array<int>
     */
    public array $databaseIds = [];

    /**
     * The displayable schedule string.
     */
    public ?string $displayableSchedule = null;

    /**
     * The next run time.
     */
    public ?string $nextRunTime = null;

    /**
     * The status of the backup configuration.
     */
    public ?string $status = null;

    /**
     * The day of week.
     */
    public ?int $dayOfWeek = null;

    /**
     * The time.
     */
    public ?string $time = null;

    /**
     * The cron schedule.
     */
    public ?string $cronSchedule = null;

    /**
     * The retention period.
     */
    public ?int $retention = null;

    /**
     * The notification email.
     */
    public ?string $notifyEmail = null;

    /**
     * Update the backup configuration.
     */
    public function update(array $data): void
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
     */
    public function delete(): void
    {
        $this->forge->deleteBackupConfiguration(
            $this->organizationId,
            $this->serverId,
            $this->id
        );
    }

    /**
     * Get backups for this configuration.
     */
    public function backups(): CursorPaginator
    {
        return $this->forge->backups(
            $this->organizationId,
            $this->serverId,
            $this->id
        );
    }

    /**
     * Create a new backup.
     */
    public function createBackup(): void
    {
        $this->forge->createBackup(
            $this->organizationId,
            $this->serverId,
            $this->id
        );
    }
}
