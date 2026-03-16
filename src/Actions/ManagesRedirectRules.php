<?php

declare(strict_types=1);

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\RedirectRule;

trait ManagesRedirectRules
{
    /**
     * Get the collection of redirect rules.
     *
     * @return RedirectRule[]
     */
    public function redirectRules(string $organizationSlug, int $serverId, int $siteId): array
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/redirect-rules")['data'] ?? [],
            RedirectRule::class,
            $organizationSlug,
            $serverId,
            $siteId,
        );
    }

    /**
     * Get a redirect rule instance.
     */
    public function redirectRule(string $organizationSlug, int $serverId, int $siteId, int $ruleId): RedirectRule
    {
        return $this->newResource(
            RedirectRule::class,
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/redirect-rules/{$ruleId}")['data'] ?? [],
            $organizationSlug,
            $serverId,
            $siteId,
        );
    }

    /**
     * Create a new redirect rule.
     */
    public function createRedirectRule(string $organizationSlug, int $serverId, int $siteId, array $data): RedirectRule
    {
        return $this->newResource(
            RedirectRule::class,
            $this->post("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/redirect-rules", $data)['data'] ?? [],
            $organizationSlug,
            $serverId,
            $siteId,
        );
    }

    /**
     * Delete the given redirect rule.
     */
    public function deleteRedirectRule(string $organizationSlug, int $serverId, int $siteId, int $ruleId): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/redirect-rules/{$ruleId}");
    }
}
