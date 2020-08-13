<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\BackupConfiguration;

trait ManagesBackups
{
    /**
     * Get the collection of backup configurations.
     *
     * @param  string  $serverId
     * @return \Laravel\Forge\Resources\BackupConfiguration[]
     */
    public function backupConfigurations($serverId)
    {
        return $this->transformCollection(
            $this->get("servers/$serverId/backup-configs")['backups'],
            BackupConfiguration::class,
            ['server_id' => $serverId]
        );
    }

    /**
     * Get a backup configuration.
     *
     * @param  string  $serverId
     * @param  string  $backupConfigurationId
     * @return \Laravel\Forge\Resources\BackupConfiguration
     */
    public function backupConfiguration($serverId, $backupConfigurationId)
    {
        return new BackupConfiguration(
            $this->get("servers/{$serverId}/backup-configs/{$backupConfigurationId}")['backup']
            + ['server_id' => $serverId], $this);
    }

    /**
     * Create a new backup configuration.
     *
     * @param  string  $serverId
     * @param  array  $data
     * @return \Laravel\Forge\Resources\BackupConfiguration
     */
    public function createBackupConfiguration($serverId, array $data)
    {
        $response = $this->post("servers/{$serverId}/backup-configs", $data);

        return new BackupConfiguration($response['backup'] + ['server_id' => $serverId], $this);
    }

    /**
     * Delete a backup configuration.
     *
     * @param  string  $serverId
     * @param  string  $backupConfigurationId
     * @return void
     */
    public function deleteBackupConfiguration($serverId, $backupConfigurationId)
    {
         $this->delete("servers/{$serverId}/backup-configs/{$backupConfigurationId}");
    }

    /**
     * Restore a backup.
     *
     * @param  string  $serverId
     * @param  string  $backupConfigurationId
     * @param  string  $backupId
     * @return void
     */
    public function restoreBackup($serverId, $backupConfigurationId, $backupId)
    {
        $this->post("servers/{$serverId}/backup-configs/{$backupConfigurationId}/backups/{$backupId}");
    }

    /**
     * Delete a backup.
     *
     * @param  string  $serverId
     * @param  string  $backupConfigurationId
     * @param  string  $backupId
     * @return void
     */
    public function deleteBackup($serverId, $backupConfigurationId, $backupId)
    {
        $this->delete("servers/{$serverId}/backup-configs/{$backupConfigurationId}/backups/{$backupId}");
    }
}
