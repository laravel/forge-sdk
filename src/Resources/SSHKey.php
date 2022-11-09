<?php

namespace Laravel\Forge\Resources;

class SSHKey extends Resource
{
    /**
     * The id of the key.
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
     * The name of the key.
     *
     * @var string
     */
    public $name;

    /**
     * The status of the key.
     *
     * @var string
     */
    public $status;

    /**
     * The username of the key.
     *
     * @var string
     */
    public $username;

    /**
     * The date/time the key was created.
     *
     * @var string
     */
    public $createdAt;

    /**
     * Delete the given key.
     *
     * @return void
     */
    public function delete()
    {
        $this->forge->deleteSSHKey($this->serverId, $this->id);
    }
}
