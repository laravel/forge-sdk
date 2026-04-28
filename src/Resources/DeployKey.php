<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class DeployKey extends Resource
{
    /**
     * The id of the organization.
     */
    public ?string $organizationId = null;

    /**
     * The id of the server.
     */
    public ?int $serverId = null;

    /**
     * The id of the site.
     */
    public ?int $siteId = null;

    /**
     * The public deploy key.
     */
    public ?string $key = null;

    /**
     * Delete the deploy key.
     */
    public function delete(): void
    {
        $this->forge->deleteDeployKey($this->organizationId, $this->serverId, $this->siteId);
    }
}
