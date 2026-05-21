<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class DeployKey extends Resource
{
    /**
     * The slug of the organization.
     */
    public string $organizationSlug;

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
        $this->forge->deleteDeployKey($this->organizationSlug, $this->serverId, $this->siteId);
    }
}
