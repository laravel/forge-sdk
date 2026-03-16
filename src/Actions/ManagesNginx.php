<?php

declare(strict_types=1);

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\NginxTemplate;

trait ManagesNginx
{
    /**
     * Get the collection of Nginx templates.
     *
     * @return NginxTemplate[]
     */
    public function nginxTemplates(string $organizationSlug, int $serverId): array
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/nginx/templates")['data'] ?? [],
            NginxTemplate::class,
            $organizationSlug,
            $serverId,
        );
    }

    /**
     * Get a Nginx template instance.
     */
    public function nginxTemplate(string $organizationSlug, int $serverId, int $templateId): NginxTemplate
    {
        return $this->newResource(
            NginxTemplate::class,
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/nginx/templates/{$templateId}")['data'] ?? [],
            $organizationSlug,
            $serverId,
        );
    }

    /**
     * Create a new Nginx template.
     */
    public function createNginxTemplate(string $organizationSlug, int $serverId, array $data): NginxTemplate
    {
        return $this->newResource(
            NginxTemplate::class,
            $this->post("orgs/{$organizationSlug}/servers/{$serverId}/nginx/templates", $data)['data'] ?? [],
            $organizationSlug,
            $serverId,
        );
    }

    /**
     * Update a Nginx template.
     */
    public function updateNginxTemplate(string $organizationSlug, int $serverId, int $templateId, array $data): NginxTemplate
    {
        return $this->newResource(
            NginxTemplate::class,
            $this->put("orgs/{$organizationSlug}/servers/{$serverId}/nginx/templates/{$templateId}", $data)['data'] ?? [],
            $organizationSlug,
            $serverId,
        );
    }

    /**
     * Delete the given Nginx template.
     */
    public function deleteNginxTemplate(string $organizationSlug, int $serverId, int $templateId): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/nginx/templates/{$templateId}");
    }
}
