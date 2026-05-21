<?php

declare(strict_types=1);

namespace Laravel\Forge\Actions;

use Laravel\Forge\CursorPaginator;
use Laravel\Forge\Resources\Backup;
use Laravel\Forge\Resources\BackupConfiguration;

trait ManagesBackups
{
    /**
     * Get the collection of backup configurations.
     */
    public function backupConfigurations(string $organizationSlug, int $serverId, array $query = []): CursorPaginator
    {
        return $this->paginatedCollection(
            "orgs/{$organizationSlug}/servers/{$serverId}/database/backups",
            BackupConfiguration::class,
            $organizationSlug,
            $serverId,
            query: $query,
        );
    }

    /**
     * Get a backup configuration instance.
     */
    public function backupConfiguration(string $organizationSlug, int $serverId, int $backupConfigurationId): BackupConfiguration
    {
        return $this->newResource(
            BackupConfiguration::class,
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/database/backups/{$backupConfigurationId}")['data'] ?? [],
            $organizationSlug,
            $serverId,
        );
    }

    /**
     * Create a new backup configuration.
     */
    public function createBackupConfiguration(string $organizationSlug, int $serverId, array $data): BackupConfiguration
    {
        return $this->newResource(
            BackupConfiguration::class,
            $this->post("orgs/{$organizationSlug}/servers/{$serverId}/database/backups", $data)['data'] ?? [],
            $organizationSlug,
            $serverId,
        );
    }

    /**
     * Update the given backup configuration.
     */
    public function updateBackupConfiguration(string $organizationSlug, int $serverId, int $backupConfigurationId, array $data): BackupConfiguration
    {
        return $this->newResource(
            BackupConfiguration::class,
            $this->put("orgs/{$organizationSlug}/servers/{$serverId}/database/backups/{$backupConfigurationId}", $data)['data'] ?? [],
            $organizationSlug,
            $serverId,
        );
    }

    /**
     * Delete the given backup configuration.
     */
    public function deleteBackupConfiguration(string $organizationSlug, int $serverId, int $backupConfigurationId): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/database/backups/{$backupConfigurationId}");
    }

    /**
     * Get the collection of backups for a backup configuration.
     */
    public function backups(string $organizationSlug, int $serverId, int $backupConfigurationId, array $query = []): CursorPaginator
    {
        return $this->paginatedCollection(
            "orgs/{$organizationSlug}/servers/{$serverId}/database/backups/{$backupConfigurationId}/instances",
            Backup::class,
            $organizationSlug,
            $serverId,
            extra: ['backup_configuration_id' => $backupConfigurationId],
            query: $query,
        );
    }

    /**
     * Get a backup instance.
     */
    public function backup(string $organizationSlug, int $serverId, int $backupConfigurationId, int $backupId): Backup
    {
        return $this->newResource(
            Backup::class,
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/database/backups/{$backupConfigurationId}/instances/{$backupId}")['data'] ?? [],
            $organizationSlug,
            $serverId,
            extra: ['backup_configuration_id' => $backupConfigurationId],
        );
    }

    /**
     * Create a new backup.
     */
    public function createBackup(string $organizationSlug, int $serverId, int $backupConfigurationId): Backup
    {
        return $this->newResource(
            Backup::class,
            $this->post("orgs/{$organizationSlug}/servers/{$serverId}/database/backups/{$backupConfigurationId}/instances")['data'] ?? [],
            $organizationSlug,
            $serverId,
            extra: ['backup_configuration_id' => $backupConfigurationId],
        );
    }

    /**
     * Delete the given backup.
     */
    public function deleteBackup(string $organizationSlug, int $serverId, int $backupConfigurationId, int $backupId): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/database/backups/{$backupConfigurationId}/instances/{$backupId}");
    }

    /**
     * Restore a backup to a database.
     */
    public function restoreBackup(string $organizationSlug, int $serverId, int $backupConfigurationId, int $backupId, array $data): void
    {
        $this->post("orgs/{$organizationSlug}/servers/{$serverId}/database/backups/{$backupConfigurationId}/instances/{$backupId}/restores", $data);
    }
}
