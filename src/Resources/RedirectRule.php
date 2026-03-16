<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class RedirectRule extends Resource
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
     * The from route of the rule.
     */
    public ?string $from = null;

    /**
     * The to route of the rule.
     */
    public ?string $to = null;

    /**
     * The type of the redirect rule.
     */
    public ?string $type = null;

    /**
     * The status of the redirect rule.
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
        $this->forge->deleteRedirectRule($this->organizationId, $this->serverId, $this->siteId, $this->id);
    }
}
