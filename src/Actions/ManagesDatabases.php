<?php

declare(strict_types=1);

namespace Laravel\Forge\Actions;

use Laravel\Forge\CursorPaginator;
use Laravel\Forge\Resources\Database;
use Laravel\Forge\Resources\DatabaseUser;

trait ManagesDatabases
{
    /**
     * Get the collection of database schemas.
     */
    public function databases(string $organizationSlug, int $serverId, array $query = []): CursorPaginator
    {
        return $this->paginatedCollection(
            "orgs/{$organizationSlug}/servers/{$serverId}/database/schemas",
            Database::class,
            $organizationSlug,
            $serverId,
            query: $query,
        );
    }

    /**
     * Get a database schema instance.
     */
    public function database(string $organizationSlug, int $serverId, int $databaseId): Database
    {
        return $this->newResource(
            Database::class,
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/database/schemas/{$databaseId}")['data'] ?? [],
            $organizationSlug,
            $serverId,
        );
    }

    /**
     * Create a new database schema.
     */
    public function createDatabase(string $organizationSlug, int $serverId, array $data, bool $wait = true): Database
    {
        $database = $this->post("orgs/{$organizationSlug}/servers/{$serverId}/database/schemas", $data)['data'] ?? [];

        if ($wait) {
            return $this->retry($this->getTimeout(), function () use ($organizationSlug, $serverId, $database) {
                $db = $this->database($organizationSlug, $serverId, (int) $database['id']);

                return isset($db->status) && $db->status === 'installed' ? $db : null;
            });
        }

        return $this->newResource(Database::class, $database, $organizationSlug, $serverId);
    }

    /**
     * Delete the given database schema.
     */
    public function deleteDatabase(string $organizationSlug, int $serverId, int $databaseId): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/database/schemas/{$databaseId}");
    }

    /**
     * Synchronize database schemas.
     */
    public function syncDatabases(string $organizationSlug, int $serverId, array $data = []): void
    {
        $this->post("orgs/{$organizationSlug}/servers/{$serverId}/database/schemas/synchronizations", $data);
    }

    /**
     * Get the collection of database users.
     */
    public function databaseUsers(string $organizationSlug, int $serverId, array $query = []): CursorPaginator
    {
        return $this->paginatedCollection(
            "orgs/{$organizationSlug}/servers/{$serverId}/database/users",
            DatabaseUser::class,
            $organizationSlug,
            $serverId,
            query: $query,
        );
    }

    /**
     * Get a database user instance.
     */
    public function databaseUser(string $organizationSlug, int $serverId, int $userId): DatabaseUser
    {
        return $this->newResource(
            DatabaseUser::class,
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/database/users/{$userId}")['data'] ?? [],
            $organizationSlug,
            $serverId,
        );
    }

    /**
     * Create a new database user.
     */
    public function createDatabaseUser(string $organizationSlug, int $serverId, array $data, bool $wait = true): DatabaseUser
    {
        $user = $this->post("orgs/{$organizationSlug}/servers/{$serverId}/database/users", $data)['data'] ?? [];

        if ($wait) {
            return $this->retry($this->getTimeout(), function () use ($organizationSlug, $serverId, $user) {
                $dbUser = $this->databaseUser($organizationSlug, $serverId, (int) $user['id']);

                return isset($dbUser->status) && $dbUser->status === 'installed' ? $dbUser : null;
            });
        }

        return $this->newResource(DatabaseUser::class, $user, $organizationSlug, $serverId);
    }

    /**
     * Update a database user.
     */
    public function updateDatabaseUser(string $organizationSlug, int $serverId, int $userId, array $data): void
    {
        $this->put("orgs/{$organizationSlug}/servers/{$serverId}/database/users/{$userId}", $data);
    }

    /**
     * Delete the given database user.
     */
    public function deleteDatabaseUser(string $organizationSlug, int $serverId, int $userId): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/database/users/{$userId}");
    }

    /**
     * Update the database password.
     */
    public function updateDatabasePassword(string $organizationSlug, int $serverId, array $data): void
    {
        $this->put("orgs/{$organizationSlug}/servers/{$serverId}/database/password", $data);
    }
}
