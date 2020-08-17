<?php

namespace Laravel\Forge\Resources;

class Database extends Resource
{
    /**
     * The id of the database.
     *
     * @var int
     */
    public $id;

    /**
     * The id of the server.
     *
     * @var int
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
     * Update the given Database.
     *
     * @param  array  $data
     * @return \Laravel\Forge\Resources\Database
     */
    public function update(array $data)
    {
        return $this->forge->updateDatabase($this->serverId, $this->id, $data);
    }

    /**
     * Delete the given database.
     *
     * @return void
     */
    public function delete()
    {
        $this->forge->deleteDatabase($this->serverId, $this->id);
    }
}
