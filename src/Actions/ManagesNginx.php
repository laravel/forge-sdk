<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\NginxTemplate;

trait ManagesNginx
{
    /**
     * Get the collection of Nginx templates.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @return \Laravel\Forge\Resources\NginxTemplate[]
     */
    public function nginxTemplates($organizationId, $serverId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/nginx/templates")['data'] ?? [],
            NginxTemplate::class,
            ['organization_id' => $organizationId, 'server_id' => $serverId]
        );
    }

    /**
     * Get a Nginx template instance.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $templateId
     * @return \Laravel\Forge\Resources\NginxTemplate
     */
    public function nginxTemplate($organizationId, $serverId, $templateId)
    {
        return new NginxTemplate(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/nginx/templates/{$templateId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a new Nginx template.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  array  $data
     * @return \Laravel\Forge\Resources\NginxTemplate
     */
    public function createNginxTemplate($organizationId, $serverId, array $data)
    {
        $template = $this->post("orgs/{$organizationId}/servers/{$serverId}/nginx/templates", $data)['data'] ?? [];

        return new NginxTemplate($template + ['organization_id' => $organizationId, 'server_id' => $serverId], $this);
    }

    /**
     * Update a Nginx template.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $templateId
     * @param  array  $data
     * @return \Laravel\Forge\Resources\NginxTemplate
     */
    public function updateNginxTemplate($organizationId, $serverId, $templateId, array $data)
    {
        $template = $this->put(
            "orgs/{$organizationId}/servers/{$serverId}/nginx/templates/{$templateId}",
            $data
        )['data'] ?? [];

        return new NginxTemplate($template, $this);
    }

    /**
     * Delete the given Nginx template.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $templateId
     * @return void
     */
    public function deleteNginxTemplate($organizationId, $serverId, $templateId)
    {
        $this->delete("orgs/{$organizationId}/servers/{$serverId}/nginx/templates/{$templateId}");
    }
}
