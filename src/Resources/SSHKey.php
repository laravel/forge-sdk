<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class SSHKey extends Resource
{
    /**
     * The id of the organization.
     */
    public ?string $organizationId = null;

    /**
     * The id of the key.
     */
    public ?int $id = null;

    /**
     * The id of the server.
     */
    public ?int $serverId = null;

    /**
     * The name of the key.
     */
    public ?string $name = null;

    /**
     * The status of the key.
     */
    public ?string $status = null;

    /**
     * The user of the SSH key.
     */
    public ?string $user = null;

    /**
     * The ID of the user who created the SSH key.
     */
    public ?int $createdBy = null;

    /**
     * The date/time the key was created.
     */
    public ?string $createdAt = null;

    /**
     * The date/time the key was last updated.
     */
    public ?string $updatedAt = null;

    /**
     * Delete the given key.
     */
    public function delete(): void
    {
        $this->forge->deleteSshKey($this->organizationId, $this->serverId, $this->id);
    }
}
