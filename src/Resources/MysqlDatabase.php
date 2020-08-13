<?php

namespace Laravel\Forge\Resources;

class MysqlDatabase extends Resource
{
    /**
     * The id of the database.
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
     * The name of the database.
     *
     * @var string
     */
    public $name;

    /**
     * The status of the database.
     *
     * @var string
     */
    public $status;

    /**
     * The date/time the database was created.
     *
     * @var string
     */
    public $createdAt;

    /**
     * Update the given MySQL Database.
     *
     * @param  array $data
     * @return MysqlDatabase
     */
    public function update(array $data)
    {
        return $this->forge->updateMysqlDatabase($this->serverId, $this->id, $data);
    }

    /**
     * Delete the given database.
     *
     * @return void
     */
    public function delete()
    {
        return $this->forge->deleteMysqlDatabase($this->serverId, $this->id);
    }
}
