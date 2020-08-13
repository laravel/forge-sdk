<?php

namespace Laravel\Forge\Resources;

class Backup extends Resource
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
    public $backupConfigurationId;

    /**
     * The status of the backup.
     *
     * @var string
     */
    public $status;

    /**
     * The status of the restore.
     *
     * @var string
     */
    public $restoreStatus;

    /**
     * The archive path.
     *
     * @var string
     */
    public $archivePath;

    /**
     * The size of the backup
     *
     * @var int
     */
    public $size;

    /**
     * The uuid of this backup
     *
     * @var string
     */
    public $uuid;

    /**
     * The duration of this backup
     *
     * @var string
     */
    public $duration;

    /**
     * The backup date.
     *
     * @var string|null
     */
    public $lastBackupTime;

    /**
     * The date/time the backup was created.
     *
     * @var string
     */
    public $createdAt;

    /**
     * Restore this backup.
     *
     *
     * @return void
     */
    public function restore()
    {
        $this->forge->restoreBackup($this->serverId, $this->backupConfigurationId, $this->id);
    }

    /**
     * Delete this backup.
     *
     * @return void
     */
    public function delete()
    {
        $this->forge->deleteBackup($this->serverId, $this->backupConfigurationId, $this->id);
    }
}
