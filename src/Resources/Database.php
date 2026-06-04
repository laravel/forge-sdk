<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class Database extends Resource
{
    /**
     * The slug of the organization.
     */
    public string $organizationSlug;

    /**
     * The id of the database.
     */
    public ?int $id = null;

    /**
     * The id of the server.
     */
    public ?int $serverId = null;

    /**
     * The name of the database.
     */
    public ?string $name = null;

    /**
     * The status of the database.
     */
    public ?string $status = null;

    /**
     * The date/time the database was created.
     */
    public ?string $createdAt = null;

    /**
     * The date/time the database was last updated.
     */
    public ?string $updatedAt = null;

    /**
     * Delete the given database.
     */
    public function delete(): void
    {
        $this->forge->deleteDatabase($this->organizationSlug, $this->serverId, $this->id);
    }
}
