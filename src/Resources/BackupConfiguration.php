<?php

namespace Themsaid\Forge\Resources;

class BackupConfiguration extends Resource
{
    /**
     * The id of the backup.
     *
     * @var integer
     */
    public $id;

    /**
     * The id of the server.
     *
     * @var integer
     */
    public $serverId;

    /**
     * The id of the database.
     *
     * @var integer
     */
    public $databaseId;

    /**
     * The day of the week: 0 (Sunday) - 6 (Saturday).
     *
     * @var int|null
     */
    public $dayOfWeek;

    /**
     * The time of the backup in 24 hour format.
     *
     * @var string
     */
    public $time;

    /**
     * The provider (s3 or spaces)
     *
     * @var boolean
     */
    public $provider;

    /**
     * The name for the provider
     *
     * @var string
     */
    public $providerName;

    /**
     * The last Backup time
     *
     * @var string|null
     */
    public $lastBackupTime;

    /**
     * The databases for this backup
     *
     * @var array
     */
    public $databases;

    /**
     * The databases for this backup
     *
     * @var array
     */
    public $logs;

    /**
     * The databases for this backup
     *
     * @var array
     */
    public $backups;


    /**
     * The date/time the certificate was created.
     *
     * @var string
     */
    public $createdAt;

    /**
     * Delete the given recipe.
     *
     * @return void
     */
    public function delete()
    {
        $this->forge->deleteBackupConfig($this->serverId, $this->id);
    }

    /**
     * Restore a backup for this configuration.
     *
     * @param $backupId
     *
     * @return void
     */
    public function restoreBackup($backupId)
    {
        $this->forge->restoreBackup($this->serverId, $this->id, $backupId);
    }

    /**
     * Delete the given recipe.
     *
     * @param $backupId
     *
     * @return void
     */
    public function deleteBackup($backupId)
    {
        $this->forge->deleteBackup($this->serverId, $this->id, $backupId);
    }
}