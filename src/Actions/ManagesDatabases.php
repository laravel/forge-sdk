<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Database;

trait ManagesDatabases
{
    /**
     * Get the collection of Databases.
     *
     * @param  int  $serverId
     * @return \Laravel\Forge\Resources\Database[]
     */
    public function databases($serverId)
    {
        return $this->transformCollection(
            $this->get("servers/$serverId/databases")['databases'],
            Database::class,
            ['server_id' => $serverId]
        );
    }

    /**
     * Get a Database instance.
     *
     * @param  int  $serverId
     * @param  int  $databaseId
     * @return \Laravel\Forge\Resources\Database
     */
    public function database($serverId, $databaseId)
    {
        return new Database(
            $this->get("servers/$serverId/databases/$databaseId")['database'] + ['server_id' => $serverId], $this
        );
    }

    /**
     * Create a new Database.
     *
     * @param  int  $serverId
     * @param  array  $data
     * @param  bool  $wait
     * @return \Laravel\Forge\Resources\Database
     */
    public function createDatabase($serverId, array $data, $wait = true)
    {
        $database = $this->post("servers/$serverId/databases", $data)['database'];

        if ($wait) {
            return $this->retry($this->getTimeout(), function () use ($serverId, $database) {
                $database = $this->database($serverId, $database['id']);

                return $database->status == 'installed' ? $database : null;
            });
        }

        return new Database($database + ['server_id' => $serverId], $this);
    }

    /**
     * Update the given Database.
     *
     * @param  int  $serverId
     * @param  int  $databaseId
     * @param  array  $data
     * @return \Laravel\Forge\Resources\Database
     */
    public function updateDatabase($serverId, $databaseId, array $data)
    {
        return new Database(
            $this->put("servers/$serverId/databases/$databaseId", $data)['database']
            + ['server_id' => $serverId], $this
        );
    }

    /**
     * Delete the given Database.
     *
     * @param  int  $serverId
     * @param  int  $databaseId
     * @return void
     */
    public function deleteDatabase($serverId, $databaseId)
    {
        $this->delete("servers/$serverId/databases/$databaseId");
    }

    /**
     * Sync the databases
     *
     * @param  int  $serverId
     * @return void
     */
    public function syncDatabases($serverId)
    {
        $this->post("servers/$serverId/databases/sync");
    }
}
