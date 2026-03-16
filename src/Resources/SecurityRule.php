<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class SecurityRule extends Resource
{
    /**
     * The id of the organization.
     */
    public ?string $organizationId = null;

    /**
     * The id of the rule.
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
     * The name of the rule.
     */
    public ?string $name = null;

    /**
     * The path to route of the rule.
     */
    public ?string $path = null;

    /**
     * The credentials of the redirect rule.
     */
    public ?string $credentials = null;

    /**
     * The status of the security rule.
     */
    public ?string $status = null;

    /**
     * The date/time the rule was created.
     */
    public ?string $createdAt = null;

    /**
     * The date/time the rule was last updated.
     */
    public ?string $updatedAt = null;

    /**
     * Delete the given redirect rule.
     */
    public function delete(): void
    {
        $this->forge->deleteSecurityRule($this->organizationId, $this->serverId, $this->siteId, $this->id);
    }
}
