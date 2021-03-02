<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\NginxTemplate;

trait ManagesNginxTemplates
{
    /**
     * Get the collection of Nginx Templates.
     *
     * @param  int  $serverId
     * @return \Laravel\Forge\Resources\NginxTemplate[]
     */
    public function nginxTemplates($serverId)
    {
        return $this->transformCollection(
            $this->get("servers/$serverId/nginx/templates")['templates'],
            NginxTemplate::class
        );
    }

    /**
     * Get a Nginx Template instance.
     *
     * @param  int  $serverId
     * @param  int  $templateId
     * @return \Laravel\Forge\Resources\NginxTemplate
     */
    public function nginxTemplate($serverId, $templateId)
    {
        return new NginxTemplate(
            $this->get("servers/$serverId/nginx/templates/$templateId")['template'], $this
        );
    }

    /**
     * Get a Nginx Default Template instance.
     *
     * @param  int  $serverId
     * @return \Laravel\Forge\Resources\NginxTemplate
     */
    public function nginxDefaultTemplate($serverId)
    {
        return new NginxTemplate(
            $this->get("servers/$serverId/nginx/templates/default"), $this
        );
    }

    /**
     * Create a new Nginx Template.
     *
     * @param  int  $serverId
     * @param  array  $data
     * @param  bool  $wait
     * @return \Laravel\Forge\Resources\NginxTemplate
     */
    public function createNginxTemplate($serverId, array $data, $wait = true)
    {
        $template = $this->post("servers/$serverId/nginx/templates", $data)['template'];

        if ($wait) {
            return $this->retry($this->getTimeout(), function () use ($serverId, $template) {
                return $this->nginxTemplate($serverId, $template['id']);
            });
        }

        return new NginxTemplate($template, $this);
    }

    /**
     * Update the given Nginx Template.
     *
     * @param  int  $serverId
     * @param  int  $templateId
     * @param  array  $data
     * @return \Laravel\Forge\Resources\NginxTemplate
     */
    public function updateNginxTemplate($serverId, $templateId, array $data)
    {
        return new NginxTemplate(
            $this->put("servers/$serverId/nginx/templates/$templateId", $data)['template'], $this
        );
    }

    /**
     * Delete the given Nginx Template.
     *
     * @param  int  $serverId
     * @param  int  $templateId
     * @return void
     */
    public function deleteNginxTemplate($serverId, $templateId)
    {
        $this->delete("servers/$serverId/nginx/templates/$templateId");
    }
}
