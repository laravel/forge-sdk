<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Deployment;
use Laravel\Forge\Resources\Webhook;

trait ManagesDeployments
{
    /**
     * Get the collection of webhooks for a site.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Webhook[]
     */
    public function webhooks($organizationId, $serverId, $siteId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/webhooks")['data'] ?? [],
            Webhook::class,
            ['organization_id' => $organizationId, 'server_id' => $serverId, 'site_id' => $siteId]
        );
    }

    /**
     * Get a webhook instance.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $webhookId
     * @return \Laravel\Forge\Resources\Webhook
     */
    public function webhook($organizationId, $serverId, $siteId, $webhookId)
    {
        return new Webhook(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/webhooks/{$webhookId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a new webhook.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Webhook
     */
    public function createWebhook($organizationId, $serverId, $siteId, array $data)
    {
        $webhook = $this->post(
            "orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/webhooks",
            $data
        )['data'] ?? [];

        return new Webhook(
            $webhook + ['organization_id' => $organizationId, 'server_id' => $serverId, 'site_id' => $siteId],
            $this
        );
    }

    /**
     * Delete the given webhook.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $webhookId
     * @return void
     */
    public function deleteWebhook($organizationId, $serverId, $siteId, $webhookId)
    {
        $this->delete("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/webhooks/{$webhookId}");
    }

    /**
     * Get the collection of deployments for a site.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Deployment[]
     */
    public function deployments($organizationId, $serverId, $siteId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/deployments")['data'] ?? [],
            Deployment::class,
            ['organization_id' => $organizationId, 'server_id' => $serverId, 'site_id' => $siteId]
        );
    }

    /**
     * Get a deployment instance.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $deploymentId
     * @return \Laravel\Forge\Resources\Deployment
     */
    public function deployment($organizationId, $serverId, $siteId, $deploymentId)
    {
        return new Deployment(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/deployments/{$deploymentId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a new deployment.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Deployment
     */
    public function createDeployment($organizationId, $serverId, $siteId, array $data = [])
    {
        $deployment = $this->post(
            "orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/deployments",
            $data
        )['data'] ?? [];

        return new Deployment(
            $deployment + ['organization_id' => $organizationId, 'server_id' => $serverId, 'site_id' => $siteId],
            $this
        );
    }

    /**
     * Get the deployment status for a site.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return array
     */
    public function deploymentStatus($organizationId, $serverId, $siteId)
    {
        return $this->get("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/deployments/status")['data'] ?? [];
    }

    /**
     * Disable quick deploy for a site.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return void
     */
    public function disableQuickDeploy($organizationId, $serverId, $siteId)
    {
        $this->delete("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/deployments/status");
    }

    /**
     * Get the deployment script for a site.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return string
     */
    public function deploymentScript($organizationId, $serverId, $siteId)
    {
        $response = $this->get("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/deployments/script");

        return $response['data']['script'] ?? $response['script'] ?? '';
    }

    /**
     * Update the deployment script for a site.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return void
     */
    public function updateDeploymentScript($organizationId, $serverId, $siteId, array $data)
    {
        $this->put("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/deployments/script", $data);
    }

    /**
     * Get the deployment trigger URL for a site.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return string
     */
    public function deploymentTriggerUrl($organizationId, $serverId, $siteId)
    {
        $response = $this->get("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/deployments/deploy-hook");

        return $response['data']['url'] ?? $response['url'] ?? '';
    }

    /**
     * Update the deployment trigger URL for a site.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return void
     */
    public function updateDeploymentTriggerUrl($organizationId, $serverId, $siteId, array $data)
    {
        $this->put("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/deployments/deploy-hook", $data);
    }

    /**
     * Enable push to deploy for a site.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return void
     */
    public function enablePushToDeploy($organizationId, $serverId, $siteId, array $data)
    {
        $this->post("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/deployments/push-to-deploy", $data);
    }

    /**
     * Disable push to deploy for a site (delete the push to deploy configuration).
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return void
     */
    public function disablePushToDeploy($organizationId, $serverId, $siteId)
    {
        $this->delete("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/deployments/push-to-deploy");
    }

    /**
     * Get the deployment log for a deployment.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $deploymentId
     * @return string
     */
    public function deploymentLog($organizationId, $serverId, $siteId, $deploymentId)
    {
        $response = $this->get("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/deployments/{$deploymentId}/log");

        return $response['data']['output'] ?? $response['output'] ?? '';
    }
}
