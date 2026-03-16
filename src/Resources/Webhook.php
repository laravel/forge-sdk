<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class Webhook extends Resource
{
    /**
     * The id of the organization.
     */
    public ?string $organizationId = null;

    /**
     * The id of the webhook.
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
     * The destination url.
     */
    public ?string $url = null;

    /**
     * The date/time the webhook was created.
     */
    public ?string $createdAt = null;

    /**
     * The date/time the webhook was last updated.
     */
    public ?string $updatedAt = null;

    /**
     * Delete the given webhook.
     */
    public function delete(): void
    {
        $this->forge->deleteWebhook($this->organizationId, $this->serverId, $this->siteId, $this->id);
    }
}
