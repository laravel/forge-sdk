<?php

declare(strict_types=1);

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Backup;
use Laravel\Forge\Resources\BackupConfiguration;

trait ManagesBackups
{
    /**
     * Get the collection of backup configurations.
     *
     * @return BackupConfiguration[]
     */
    public function backupConfigurations(string $organizationSlug, int $serverId): array
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/database/backups")['data'] ?? [],
            BackupConfiguration::class,
            $organizationSlug,
            $serverId,
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
    public function createBackupConfiguration(string $organizationSlug, int $serverId, array $data): void
    {
        $this->post("orgs/{$organizationSlug}/servers/{$serverId}/database/backups", $data);
    }

    /**
     * Update the given backup configuration.
     */
    public function updateBackupConfiguration(string $organizationSlug, int $serverId, int $backupConfigurationId, array $data): void
    {
        $this->put("orgs/{$organizationSlug}/servers/{$serverId}/database/backups/{$backupConfigurationId}", $data);
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
     *
     * @return Backup[]
     */
    public function backups(string $organizationSlug, int $serverId, int $backupConfigurationId): array
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/database/backups/{$backupConfigurationId}/instances")['data'] ?? [],
            Backup::class,
            $organizationSlug,
            $serverId,
            extra: ['backup_configuration_id' => $backupConfigurationId],
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
    public function createBackup(string $organizationSlug, int $serverId, int $backupConfigurationId): void
    {
        $this->post("orgs/{$organizationSlug}/servers/{$serverId}/database/backups/{$backupConfigurationId}/instances");
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
