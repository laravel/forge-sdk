<?php

declare(strict_types=1);

namespace Laravel\Forge\Actions;

use Laravel\Forge\CursorPaginator;
use Laravel\Forge\Resources\FirewallRule;

trait ManagesFirewallRules
{
    /**
     * Get the collection of firewall rules.
     */
    public function firewallRules(string $organizationSlug, int $serverId, array $query = []): CursorPaginator
    {
        return $this->paginatedCollection(
            "orgs/{$organizationSlug}/servers/{$serverId}/firewall-rules",
            FirewallRule::class,
            $organizationSlug,
            $serverId,
            query: $query,
        );
    }

    /**
     * Get a firewall rule instance.
     */
    public function firewallRule(string $organizationSlug, int $serverId, int $ruleId): FirewallRule
    {
        return $this->newResource(
            FirewallRule::class,
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/firewall-rules/{$ruleId}")['data'] ?? [],
            $organizationSlug,
            $serverId,
        );
    }

    /**
     * Create a new firewall rule.
     */
    public function createFirewallRule(string $organizationSlug, int $serverId, array $data): void
    {
        $this->post("orgs/{$organizationSlug}/servers/{$serverId}/firewall-rules", $data);
    }

    /**
     * Delete the given firewall rule.
     */
    public function deleteFirewallRule(string $organizationSlug, int $serverId, int $ruleId): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/firewall-rules/{$ruleId}");
    }
}
