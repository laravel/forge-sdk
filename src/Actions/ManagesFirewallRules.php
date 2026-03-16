<?php

declare(strict_types=1);

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\FirewallRule;

trait ManagesFirewallRules
{
    /**
     * Get the collection of firewall rules.
     *
     * @return FirewallRule[]
     */
    public function firewallRules(string $organizationSlug, int $serverId): array
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/firewall-rules")['data'] ?? [],
            FirewallRule::class,
            $organizationSlug,
            $serverId,
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
    public function createFirewallRule(string $organizationSlug, int $serverId, array $data): FirewallRule
    {
        return $this->newResource(
            FirewallRule::class,
            $this->post("orgs/{$organizationSlug}/servers/{$serverId}/firewall-rules", $data)['data'] ?? [],
            $organizationSlug,
            $serverId,
        );
    }

    /**
     * Delete the given firewall rule.
     */
    public function deleteFirewallRule(string $organizationSlug, int $serverId, int $ruleId): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/firewall-rules/{$ruleId}");
    }
}
