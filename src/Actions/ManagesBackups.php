<?php

namespace Themsaid\Forge\Actions;

use Themsaid\Forge\Resources\BackupConfiguration;
use Themsaid\Forge\Resources\Event;
use Themsaid\Forge\Resources\Server;

trait ManagesBackups
{
    /**
     * Get the collection of backup configurations.
     *
     * @param  string $serverId
     * @return \Themsaid\Forge\Resources\BackupConfiguration[]
     */
    public function backupConfigs($serverId)
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
     * @param  string $serverId
     * @param  string $backupConfigurationId
     * @return BackupConfiguration
     */
    public function backupConfig($serverId, $backupConfigurationId)
    {
        return new BackupConfiguration(
            $this->get("servers/{$serverId}/backup-configs/{$backupConfigurationId}")['backup']
            + ['server_id' => $serverId], $this);
    }

    /**
     * Create a new backup configuration.
     *
     * @param  string $serverId
     * @param  array $data
     * @return BackupConfiguration
     */
    public function createBackupConfig($serverId, array $data)
    {
        $response = $this->post("servers/{$serverId}/backup-configs", $data);

        return new BackupConfiguration($response['backup']+ ['server_id' => $serverId], $this);
    }

    /**
     * Delete a backup configuration.
     *
     * @param  string $serverId
     * @param  string $backupConfigurationId
     */
    public function deleteBackupConfig($serverId, $backupConfigurationId)
    {
         $this->delete("servers/{$serverId}/backup-configs/{$backupConfigurationId}");
    }

    /**
     * Restore a backup.
     *
     * @param  string $serverId
     * @param  string $backupConfigurationId
     * @param  string $backupId
     */
    public function restoreBackup($serverId, $backupConfigurationId, $backupId)
    {
        $this->post("servers/{$serverId}/backup-configs/{$backupConfigurationId}/backups/{$backupId}");
    }

    /**
     * Delete a backup.
     *
     * @param  string $serverId
     * @param  string $backupConfigurationId
     * @param  string $backupId
     */
    public function deleteBackup($serverId, $backupConfigurationId, $backupId)
    {
        $this->delete("servers/{$serverId}/backup-configs/{$backupConfigurationId}/backups/{$backupId}");
    }
}
