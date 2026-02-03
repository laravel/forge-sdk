<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Backup;
use Laravel\Forge\Resources\BackupConfiguration;

trait ManagesBackups
{
    /**
     * Get the collection of backup configurations.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @return \Laravel\Forge\Resources\BackupConfiguration[]
     */
    public function backupConfigurations($organizationId, $serverId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/database/backups")['data'] ?? [],
            BackupConfiguration::class,
            ['organization_id' => $organizationId, 'server_id' => $serverId]
        );
    }

    /**
     * Get a backup configuration instance.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $backupConfigurationId
     * @return \Laravel\Forge\Resources\BackupConfiguration
     */
    public function backupConfiguration($organizationId, $serverId, $backupConfigurationId)
    {
        return new BackupConfiguration(
            ($this->get("orgs/{$organizationId}/servers/{$serverId}/database/backups/{$backupConfigurationId}")['data'] ?? [])
                + ['organization_id' => $organizationId, 'server_id' => $serverId],
            $this
        );
    }

    /**
     * Create a new backup configuration.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @return void
     */
    public function createBackupConfiguration($organizationId, $serverId, array $data)
    {
        $this->post("orgs/{$organizationId}/servers/{$serverId}/database/backups", $data);
    }

    /**
     * Update the given backup configuration.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $backupConfigurationId
     * @return void
     */
    public function updateBackupConfiguration($organizationId, $serverId, $backupConfigurationId, array $data)
    {
        $this->put("orgs/{$organizationId}/servers/{$serverId}/database/backups/{$backupConfigurationId}", $data);
    }

    /**
     * Delete the given backup configuration.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $backupConfigurationId
     * @return void
     */
    public function deleteBackupConfiguration($organizationId, $serverId, $backupConfigurationId)
    {
        $this->delete("orgs/{$organizationId}/servers/{$serverId}/database/backups/{$backupConfigurationId}");
    }

    /**
     * Get the collection of backups for a backup configuration.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $backupConfigurationId
     * @return \Laravel\Forge\Resources\Backup[]
     */
    public function backups($organizationId, $serverId, $backupConfigurationId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/database/backups/{$backupConfigurationId}/instances")['data'] ?? [],
            Backup::class,
            ['organization_id' => $organizationId, 'server_id' => $serverId, 'backup_configuration_id' => $backupConfigurationId]
        );
    }

    /**
     * Get a backup instance.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $backupConfigurationId
     * @param  string  $backupId
     * @return \Laravel\Forge\Resources\Backup
     */
    public function backup($organizationId, $serverId, $backupConfigurationId, $backupId)
    {
        return new Backup(
            ($this->get("orgs/{$organizationId}/servers/{$serverId}/database/backups/{$backupConfigurationId}/instances/{$backupId}")['data'] ?? [])
                + ['organization_id' => $organizationId, 'server_id' => $serverId, 'backup_configuration_id' => $backupConfigurationId],
            $this
        );
    }

    /**
     * Create a new backup.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $backupConfigurationId
     * @return void
     */
    public function createBackup($organizationId, $serverId, $backupConfigurationId)
    {
        $this->post("orgs/{$organizationId}/servers/{$serverId}/database/backups/{$backupConfigurationId}/instances");
    }

    /**
     * Delete the given backup.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $backupConfigurationId
     * @param  string  $backupId
     * @return void
     */
    public function deleteBackup($organizationId, $serverId, $backupConfigurationId, $backupId)
    {
        $this->delete("orgs/{$organizationId}/servers/{$serverId}/database/backups/{$backupConfigurationId}/instances/{$backupId}");
    }

    /**
     * Restore a backup to a database.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $backupConfigurationId
     * @param  string  $backupId
     * @return void
     */
    public function restoreBackup($organizationId, $serverId, $backupConfigurationId, $backupId, array $data)
    {
        $this->post("orgs/{$organizationId}/servers/{$serverId}/database/backups/{$backupConfigurationId}/instances/{$backupId}/restores", $data);
    }
}
