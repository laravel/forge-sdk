<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\MysqlDatabase;

trait ManagesMysqlDatabases
{
    /**
     * Get the collection of MySQL Databases.
     *
     * @param  integer $serverId
     * @return MysqlDatabase[]
     */
    public function mysqlDatabases($serverId)
    {
        return $this->transformCollection(
            $this->get("servers/$serverId/mysql")['databases'],
            MysqlDatabase::class,
            ['server_id' => $serverId]
        );
    }

    /**
     * Get a MySQL Database instance.
     *
     * @param  integer $serverId
     * @param  integer $databaseId
     * @return MysqlDatabase
     */
    public function mysqlDatabase($serverId, $databaseId)
    {
        return new MysqlDatabase(
            $this->get("servers/$serverId/mysql/$databaseId")['database'] + ['server_id' => $serverId], $this
        );
    }

    /**
     * Create a new MySQL Database.
     *
     * @param  integer $serverId
     * @param  array $data
     * @param  boolean $wait
     * @return MysqlDatabase
     */
    public function createMysqlDatabase($serverId, array $data, $wait = true)
    {
        $database = $this->post("servers/$serverId/mysql", $data)['database'];

        if ($wait) {
            return $this->retry(30, function () use ($serverId, $database) {
                $database = $this->mysqlDatabase($serverId, $database['id']);

                return $database->status == 'installed' ? $database : null;
            });
        }

        return new MysqlDatabase($database + ['server_id' => $serverId], $this);
    }

    /**
     * Update the given MySQL Database.
     *
     * @param  integer $serverId
     * @param  integer $databaseId
     * @param  array $data
     * @return MysqlDatabase
     */
    public function updateMysqlDatabase($serverId, $databaseId, array $data)
    {
        return new MysqlDatabase(
            $this->put("servers/$serverId/mysql/$databaseId", $data)['database']
            + ['server_id' => $serverId], $this
        );
    }

    /**
     * Delete the given database.
     *
     * @param  integer $serverId
     * @param  integer $databaseId
     * @return void
     */
    public function deleteMysqlDatabase($serverId, $databaseId)
    {
        $this->delete("servers/$serverId/mysql/$databaseId");
    }
}