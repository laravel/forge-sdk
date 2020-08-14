<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\MysqlUser;

trait ManagesMysqlUsers
{
    /**
     * Get the collection of MySQL users.
     *
     * @param  int  $serverId
     * @return \Laravel\Forge\Resources\MysqlUser[]
     */
    public function mysqlUsers($serverId)
    {
        return $this->transformCollection(
            $this->get("servers/$serverId/mysql-users")['users'],
            MysqlUser::class,
            ['server_id' => $serverId]
        );
    }

    /**
     * Get a MySQL user instance.
     *
     * @param  int  $serverId
     * @param  int  $userId
     * @return \Laravel\Forge\Resources\MysqlUser
     */
    public function mysqlUser($serverId, $userId)
    {
        return new MysqlUser(
            $this->get("servers/$serverId/mysql-users/$userId")['user'] + ['server_id' => $serverId], $this
        );
    }

    /**
     * Create a new MySQL User.
     *
     * @param  int  $serverId
     * @param  array  $data
     * @param  bool  $wait
     * @return \Laravel\Forge\Resources\MysqlUser
     */
    public function createMysqlUser($serverId, array $data, $wait = true)
    {
        $user = $this->post("servers/$serverId/mysql-users", $data)['user'];

        if ($wait) {
            return $this->retry($this->getTimeout(), function () use ($serverId, $user) {
                $user = $this->mysqlUser($serverId, $user['id']);

                return $user->status == 'installed' ? $user : null;
            });
        }

        return new MysqlUser($user + ['server_id' => $serverId], $this);
    }

    /**
     * Update the given MySQL User.
     *
     * @param  int  $serverId
     * @param  int  $userId
     * @param  array  $data
     * @return \Laravel\Forge\Resources\MysqlUser
     */
    public function updateMysqlUser($serverId, $userId, array $data)
    {
        return new MysqlUser(
            $this->put("servers/$serverId/mysql-users/$userId", $data)['user']
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
    public function deleteMysqlUser($serverId, $userId)
    {
        $this->delete("servers/$serverId/mysql-users/$userId");
    }
}
