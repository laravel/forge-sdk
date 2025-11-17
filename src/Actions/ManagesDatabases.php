<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Database;
use Laravel\Forge\Resources\DatabaseUser;

trait ManagesDatabases
{
    /**
     * Get the collection of database schemas.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @return \Laravel\Forge\Resources\Database[]
     */
    public function databases($organizationId, $serverId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/database/schemas")['data'] ?? [],
            Database::class,
            ['organization_id' => $organizationId, 'server_id' => $serverId]
        );
    }

    /**
     * Get a database schema instance.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $databaseId
     * @return \Laravel\Forge\Resources\Database
     */
    public function database($organizationId, $serverId, $databaseId)
    {
        return new Database(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/database/schemas/{$databaseId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a new database schema.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  array  $data
     * @param  bool  $wait
     * @return \Laravel\Forge\Resources\Database
     */
    public function createDatabase($organizationId, $serverId, array $data, $wait = true)
    {
        $database = $this->post("orgs/{$organizationId}/servers/{$serverId}/database/schemas", $data)['data'] ?? [];

        if ($wait) {
            return $this->retry($this->getTimeout(), function () use ($organizationId, $serverId, $database) {
                $db = $this->database($organizationId, $serverId, $database['id']);
                return isset($db->status) && $db->status === 'installed' ? $db : null;
            });
        }

        return new Database($database + ['organization_id' => $organizationId, 'server_id' => $serverId], $this);
    }

    /**
     * Delete the given database schema.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $databaseId
     * @return void
     */
    public function deleteDatabase($organizationId, $serverId, $databaseId)
    {
        $this->delete("orgs/{$organizationId}/servers/{$serverId}/database/schemas/{$databaseId}");
    }

    /**
     * Synchronize database schemas.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  array  $data
     * @return mixed
     */
    public function syncDatabases($organizationId, $serverId, array $data = [])
    {
        return $this->post("orgs/{$organizationId}/servers/{$serverId}/database/schemas/synchronizations", $data);
    }

    /**
     * Get the collection of database users.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @return \Laravel\Forge\Resources\DatabaseUser[]
     */
    public function databaseUsers($organizationId, $serverId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/database/users")['data'] ?? [],
            DatabaseUser::class,
            ['organization_id' => $organizationId, 'server_id' => $serverId]
        );
    }

    /**
     * Get a database user instance.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $userId
     * @return \Laravel\Forge\Resources\DatabaseUser
     */
    public function databaseUser($organizationId, $serverId, $userId)
    {
        return new DatabaseUser(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/database/users/{$userId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a new database user.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  array  $data
     * @param  bool  $wait
     * @return \Laravel\Forge\Resources\DatabaseUser
     */
    public function createDatabaseUser($organizationId, $serverId, array $data, $wait = true)
    {
        $user = $this->post("orgs/{$organizationId}/servers/{$serverId}/database/users", $data)['data'] ?? [];

        if ($wait) {
            return $this->retry($this->getTimeout(), function () use ($organizationId, $serverId, $user) {
                $dbUser = $this->databaseUser($organizationId, $serverId, $user['id']);
                return isset($dbUser->status) && $dbUser->status === 'installed' ? $dbUser : null;
            });
        }

        return new DatabaseUser($user + ['organization_id' => $organizationId, 'server_id' => $serverId], $this);
    }

    /**
     * Update a database user.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $userId
     * @param  array  $data
     * @return \Laravel\Forge\Resources\DatabaseUser
     */
    public function updateDatabaseUser($organizationId, $serverId, $userId, array $data)
    {
        $user = $this->put(
            "orgs/{$organizationId}/servers/{$serverId}/database/users/{$userId}",
            $data
        )['data'] ?? [];

        return new DatabaseUser($user, $this);
    }

    /**
     * Delete the given database user.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $userId
     * @return void
     */
    public function deleteDatabaseUser($organizationId, $serverId, $userId)
    {
        $this->delete("orgs/{$organizationId}/servers/{$serverId}/database/users/{$userId}");
    }

    /**
     * Update the database password.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  array  $data
     * @return mixed
     */
    public function updateDatabasePassword($organizationId, $serverId, array $data)
    {
        return $this->put("orgs/{$organizationId}/servers/{$serverId}/database/password", $data);
    }
}
