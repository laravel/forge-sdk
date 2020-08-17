<?php

namespace Laravel\Forge\Resources;

class DatabaseUser extends Resource
{
    /**
     * The id of the database user.
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
     * The name of the database user.
     *
     * @var string
     */
    public $name;

    /**
     * The status of the database user.
     *
     * @var string
     */
    public $status;

    /**
     * The date/time the database user was created.
     *
     * @var string
     */
    public $createdAt;

    /**
     * Update the given Database User.
     *
     * @param  array  $data
     * @return \Laravel\Forge\Resources\DatabaseUser
     */
    public function update(array $data)
    {
        return $this->forge->updateDatabaseUser($this->serverId, $this->id, $data);
    }

    /**
     * Delete the given user.
     *
     * @return void
     */
    public function delete()
    {
        $this->forge->deleteDatabaseUser($this->serverId, $this->id);
    }
}
