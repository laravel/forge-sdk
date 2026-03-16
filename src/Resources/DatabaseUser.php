<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class DatabaseUser extends Resource
{
    /**
     * The id of the organization.
     */
    public ?string $organizationId = null;

    /**
     * The id of the database user.
     */
    public ?int $id = null;

    /**
     * The id of the server.
     */
    public ?int $serverId = null;

    /**
     * The name of the database user.
     */
    public ?string $name = null;

    /**
     * The status of the database user.
     */
    public ?string $status = null;

    /**
     * The databases the user has access to.
     */
    public array $databases = [];

    /**
     * The date/time the database user was created.
     */
    public ?string $createdAt = null;

    /**
     * The date/time the database user was last updated.
     */
    public ?string $updatedAt = null;

    /**
     * Update the given Database User.
     */
    public function update(array $data): DatabaseUser
    {
        return $this->forge->updateDatabaseUser($this->organizationId, $this->serverId, $this->id, $data);
    }

    /**
     * Delete the given user.
     */
    public function delete(): void
    {
        $this->forge->deleteDatabaseUser($this->organizationId, $this->serverId, $this->id);
    }
}
