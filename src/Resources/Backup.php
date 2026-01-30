<?php

namespace Laravel\Forge\Resources;

class Backup extends Resource
{
    /**
     * The id of the backup.
     *
     * @var int
     */
    public $id;

    /**
     * The id of the organization.
     *
     * @var string
     */
    public $organizationId;

    /**
     * The id of the server.
     *
     * @var int
     */
    public $serverId;

    /**
     * The id of the backup configuration.
     *
     * @var int
     */
    public $backupConfigurationId;

    /**
     * The status of the backup.
     *
     * @var string
     */
    public $status;

    /**
     * Whether the backup is partial.
     *
     * @var bool
     */
    public $isPartial;

    /**
     * The size of the backup.
     *
     * @var int|null
     */
    public $size;

    /**
     * The date/time the backup finished.
     *
     * @var string|null
     */
    public $finishedAt;

    /**
     * Delete the backup.
     *
     * @return void
     */
    public function delete()
    {
        $this->forge->deleteBackup(
            $this->organizationId,
            $this->serverId,
            $this->backupConfigurationId,
            $this->id
        );
    }

    /**
     * Restore the backup to a database.
     *
     * @param  int  $databaseId
     * @return void
     */
    public function restore($databaseId)
    {
        $this->forge->restoreBackup(
            $this->organizationId,
            $this->serverId,
            $this->backupConfigurationId,
            $this->id,
            ['database_id' => $databaseId]
        );
    }
}
