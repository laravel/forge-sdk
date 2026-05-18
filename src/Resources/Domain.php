<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class Domain extends Resource
{
    /**
     * The id of the organization.
     */
    public ?string $organizationId = null;

    /**
     * The id of the domain.
     */
    public ?int $id = null;

    /**
     * The id of the server.
     */
    public ?int $serverId = null;

    /**
     * The id of the site.
     */
    public ?int $siteId = null;

    /**
     * The domain name.
     */
    public ?string $name = null;

    /**
     * The status of the domain.
     */
    public ?string $status = null;

    /**
     * The type of the domain.
     */
    public ?string $type = null;

    /**
     * The www redirect type of the domain.
     */
    public ?string $wwwRedirectType = null;

    /**
     * Whether the domain allows wildcard subdomains.
     */
    public ?bool $allowWildcardSubdomains = null;

    /**
     * The date/time the domain was created.
     */
    public ?string $createdAt = null;

    /**
     * The date/time the domain was last updated.
     */
    public ?string $updatedAt = null;

    /**
     * Delete the given domain.
     */
    public function delete(): void
    {
        $this->forge->deleteDomain($this->organizationId, $this->serverId, $this->siteId, $this->id);
    }
}
