<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\NginxTemplate;

trait ManagesNginx
{
    /**
     * Get the collection of Nginx templates.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return \Laravel\Forge\Resources\NginxTemplate[]
     */
    public function nginxTemplates($organizationSlug, $serverId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/nginx/templates")['data'] ?? [],
            NginxTemplate::class,
            ['organization_id' => $organizationSlug, 'server_id' => $serverId]
        );
    }

    /**
     * Get a Nginx template instance.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $templateId
     * @return \Laravel\Forge\Resources\NginxTemplate
     */
    public function nginxTemplate($organizationSlug, $serverId, $templateId)
    {
        return new NginxTemplate(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/nginx/templates/{$templateId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a new Nginx template.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return \Laravel\Forge\Resources\NginxTemplate
     */
    public function createNginxTemplate($organizationSlug, $serverId, array $data)
    {
        $template = $this->post("orgs/{$organizationSlug}/servers/{$serverId}/nginx/templates", $data)['data'] ?? [];

        return new NginxTemplate($template + ['organization_id' => $organizationSlug, 'server_id' => $serverId], $this);
    }

    /**
     * Update a Nginx template.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $templateId
     * @return \Laravel\Forge\Resources\NginxTemplate
     */
    public function updateNginxTemplate($organizationSlug, $serverId, $templateId, array $data)
    {
        $template = $this->put(
            "orgs/{$organizationSlug}/servers/{$serverId}/nginx/templates/{$templateId}",
            $data
        )['data'] ?? [];

        return new NginxTemplate($template, $this);
    }

    /**
     * Delete the given Nginx template.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $templateId
     * @return void
     */
    public function deleteNginxTemplate($organizationSlug, $serverId, $templateId)
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/nginx/templates/{$templateId}");
    }
}
