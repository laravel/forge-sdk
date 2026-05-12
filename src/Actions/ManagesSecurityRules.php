<?php

declare(strict_types=1);

namespace Laravel\Forge\Actions;

use Laravel\Forge\CursorPaginator;
use Laravel\Forge\Resources\SecurityRule;

trait ManagesSecurityRules
{
    /**
     * Get the collection of security rules.
     */
    public function securityRules(string $organizationSlug, int $serverId, int $siteId, array $query = []): CursorPaginator
    {
        return $this->paginatedCollection(
            "orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/security-rules",
            SecurityRule::class,
            $organizationSlug,
            $serverId,
            $siteId,
            query: $query,
        );
    }

    /**
     * Get a security rule instance.
     */
    public function securityRule(string $organizationSlug, int $serverId, int $siteId, int $ruleId): SecurityRule
    {
        return $this->newResource(
            SecurityRule::class,
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/security-rules/{$ruleId}")['data'] ?? [],
            $organizationSlug,
            $serverId,
            $siteId,
        );
    }

    /**
     * Create a new security rule.
     */
    public function createSecurityRule(string $organizationSlug, int $serverId, int $siteId, array $data): SecurityRule
    {
        return $this->newResource(
            SecurityRule::class,
            $this->post("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/security-rules", $data)['data'] ?? [],
            $organizationSlug,
            $serverId,
            $siteId,
        );
    }

    /**
     * Update a security rule.
     */
    public function updateSecurityRule(string $organizationSlug, int $serverId, int $siteId, int $ruleId, array $data): void
    {
        $this->put("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/security-rules/{$ruleId}", $data);
    }

    /**
     * Delete the given security rule.
     */
    public function deleteSecurityRule(string $organizationSlug, int $serverId, int $siteId, int $ruleId): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/security-rules/{$ruleId}");
    }
}
