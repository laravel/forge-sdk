<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\DatabaseUser;

trait ManagesDatabaseUsers
{
    /**
     * Get the collection of Database Users.
     *
     * @param  int  $serverId
     * @return DatabaseUser[]
     */
    public function databaseUsers($serverId)
    {
        return $this->transformCollection(
            $this->get("servers/$serverId/database-users")['users'],
            DatabaseUser::class,
            ['server_id' => $serverId]
        );
    }

    /**
     * Get a Database User instance.
     *
     * @param  int  $serverId
     * @param  int  $userId
     * @return DatabaseUser
     */
    public function databaseUser($serverId, $userId)
    {
        return new DatabaseUser(
            $this->get("servers/$serverId/database-users/$userId")['user'] + ['server_id' => $serverId], $this
        );
    }

    /**
     * Create a new Database User.
     *
     * @param  int  $serverId
     * @param  bool  $wait
     * @return DatabaseUser
     */
    public function createDatabaseUser($serverId, array $data, $wait = true)
    {
        $user = $this->post("servers/$serverId/database-users", $data)['user'];

        if ($wait) {
            return $this->retry($this->getTimeout(), function () use ($serverId, $user) {
                $user = $this->databaseUser($serverId, $user['id']);

                return $user->status == 'installed' ? $user : null;
            });
        }

        return new DatabaseUser($user + ['server_id' => $serverId], $this);
    }

    /**
     * Update the given Database User.
     *
     * @param  int  $serverId
     * @param  int  $userId
     * @return DatabaseUser
     */
    public function updateDatabaseUser($serverId, $userId, array $data)
    {
        return new DatabaseUser(
            $this->put("servers/$serverId/database-users/$userId", $data)['user']
            + ['server_id' => $serverId], $this
        );
    }

    /**
     * Delete the given user.
     *
     * @param  int  $serverId
     * @param  int  $userId
     * @return void
     */
    public function deleteDatabaseUser($serverId, $userId)
    {
        $this->delete("servers/$serverId/database-users/$userId");
    }
}
