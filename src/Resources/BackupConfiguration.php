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
     * The databases for this backup.
     *
     * Note: this is only available when getting a single configuration.
     *
     * @var \Themsaid\Forge\Resources\MysqlDatabase[]
     */
    public $databases;

    /**
     * The databases for this backup
     *
     * @var \Themsaid\Forge\Resources\Backup[]
     */
    public $backups;

    /**
     * The date/time the configuration was created.
     *
     * @var string
     */
    public $createdAt;

    public function __construct(array $attributes, $forge = null)
    {
        parent::__construct($attributes, $forge);

        $this->databases = $this->transformCollection(
            $this->databases ?: [],
            MysqlDatabase::class,
            ['server_id' => $this->serverId]
        );

        $this->backups = $this->transformCollection(
            $this->backups ?: [],
            Backup::class,
            ['server_id' => $this->serverId]
        );
    }

    /**
     * Delete the given configuration.
     *
     * @return void
     */
    public function delete()
    {
        $this->forge->deleteBackupConfiguration($this->serverId, $this->id);
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
     * Delete the given backup.
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
