<?php

declare(strict_types=1);

namespace Laravel\Forge\Actions;

use Laravel\Forge\CursorPaginator;
use Laravel\Forge\Resources\RedirectRule;

trait ManagesRedirectRules
{
    /**
     * Get the collection of redirect rules.
     */
    public function redirectRules(string $organizationSlug, int $serverId, int $siteId, array $query = []): CursorPaginator
    {
        return $this->paginatedCollection(
            "orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/redirect-rules",
            RedirectRule::class,
            $organizationSlug,
            $serverId,
            $siteId,
            query: $query,
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
    public function createRedirectRule(string $organizationSlug, int $serverId, int $siteId, array $data): void
    {
        $this->post("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/redirect-rules", $data);
    }

    /**
     * Delete the given redirect rule.
     */
    public function deleteRedirectRule(string $organizationSlug, int $serverId, int $siteId, int $ruleId): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/redirect-rules/{$ruleId}");
    }

    /**
     * Update the order of the site's redirect rules.
     */
    public function updateReorder(string $organizationSlug, int $serverId, int $siteId, array $data): void
    {
        $this->put("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/redirect-rules/reorder", $data);
    }

    /**
     * Export the site's redirect rules as CSV.
     */
    public function export(string $organizationSlug, int $serverId, int $siteId): string
    {
        return $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/redirect-rules/export");
    }

    /**
     * Import redirect rules from CSV contents.
     */
    public function createImport(string $organizationSlug, int $serverId, int $siteId, string $csv, string $mode = 'append'): array
    {
        return $this->postMultipart(
            "orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/redirect-rules/import",
            [
                ['name' => 'file', 'contents' => $csv, 'filename' => 'redirects.csv'],
                ['name' => 'mode', 'contents' => $mode],
            ],
        ) ?? [];
    }
}
