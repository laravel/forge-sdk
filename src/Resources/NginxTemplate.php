<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class NginxTemplate extends Resource
{
    /**
     * The id of the organization.
     */
    public ?string $organizationId = null;

    /**
     * The id of the nginx template.
     */
    public ?int $id = null;

    /**
     * The id of the server.
     */
    public ?int $serverId = null;

    /**
     * The name of the nginx template.
     */
    public ?string $name = null;

    /**
     * The content of the nginx template.
     */
    public ?string $content = null;

    /**
     * The date/time the nginx template was created.
     */
    public ?string $createdAt = null;

    /**
     * The date/time the nginx template was last updated.
     */
    public ?string $updatedAt = null;

    /**
     * Update the given nginx template.
     */
    public function update(array $data): NginxTemplate
    {
        return $this->forge->updateNginxTemplate($this->organizationId, $this->serverId, $this->id, $data);
    }

    /**
     * Delete the given nginx template.
     */
    public function delete(): void
    {
        $this->forge->deleteNginxTemplate($this->organizationId, $this->serverId, $this->id);
    }
}
