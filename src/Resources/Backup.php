<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class Backup extends Resource
{
    /**
     * The id of the backup.
     */
    public ?int $id = null;

    /**
     * The slug of the organization.
     */
    public string $organizationSlug;

    /**
     * The id of the server.
     */
    public ?int $serverId = null;

    /**
     * The id of the backup configuration.
     */
    public ?int $backupConfigurationId = null;

    /**
     * The status of the backup.
     */
    public ?string $status = null;

    /**
     * Whether the backup is partial (the API returns a string flag).
     */
    public ?string $isPartial = null;

    /**
     * The size of the backup.
     */
    public ?int $size = null;

    /**
     * The date/time the backup finished.
     */
    public ?string $finishedAt = null;

    /**
     * Delete the backup.
     */
    public function delete(): void
    {
        $this->forge->deleteBackup(
            $this->organizationSlug,
            $this->serverId,
            $this->backupConfigurationId,
            $this->id
        );
    }

    /**
     * Restore the backup to a database.
     */
    public function restore(int $databaseId): void
    {
        $this->forge->restoreBackup(
            $this->organizationSlug,
            $this->serverId,
            $this->backupConfigurationId,
            $this->id,
            ['database_id' => $databaseId]
        );
    }
}
