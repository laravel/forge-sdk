<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class FirewallRule extends Resource
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
     * The name of the rule.
     */
    public ?string $name = null;

    /**
     * The port number used.
     */
    public ?int $port = null;

    /**
     * The IP Address.
     */
    public ?string $ipAddress = null;

    /**
     * The status of the rule.
     */
    public ?string $status = null;

    /**
     * The type of the firewall rule.
     */
    public ?string $type = null;

    /**
     * The date/time the rule was created.
     */
    public ?string $createdAt = null;

    /**
     * The date/time the rule was last updated.
     */
    public ?string $updatedAt = null;

    /**
     * Delete the given firewall rule.
     */
    public function delete(): void
    {
        $this->forge->deleteFirewallRule($this->organizationId, $this->serverId, $this->id);
    }
}
