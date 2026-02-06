<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Deployment;
use Laravel\Forge\Resources\Webhook;

trait ManagesDeployments
{
    /**
     * Get the collection of webhooks for a site.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Webhook[]
     */
    public function webhooks($organizationSlug, $serverId, $siteId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/webhooks")['data'] ?? [],
            Webhook::class,
            ['organization_id' => $organizationSlug, 'server_id' => $serverId, 'site_id' => $siteId]
        );
    }

    /**
     * Get a webhook instance.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $webhookId
     * @return \Laravel\Forge\Resources\Webhook
     */
    public function webhook($organizationSlug, $serverId, $siteId, $webhookId)
    {
        return new Webhook(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/webhooks/{$webhookId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a new webhook.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Webhook
     */
    public function createWebhook($organizationSlug, $serverId, $siteId, array $data)
    {
        $webhook = $this->post(
            "orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/webhooks",
            $data
        )['data'] ?? [];

        return new Webhook(
            $webhook + ['organization_id' => $organizationSlug, 'server_id' => $serverId, 'site_id' => $siteId],
            $this
        );
    }

    /**
     * Delete the given webhook.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $webhookId
     * @return void
     */
    public function deleteWebhook($organizationSlug, $serverId, $siteId, $webhookId)
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/webhooks/{$webhookId}");
    }

    /**
     * Get the collection of deployments for a site.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Deployment[]
     */
    public function deployments($organizationSlug, $serverId, $siteId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/deployments")['data'] ?? [],
            Deployment::class,
            ['organization_id' => $organizationSlug, 'server_id' => $serverId, 'site_id' => $siteId]
        );
    }

    /**
     * Get a deployment instance.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $deploymentId
     * @return \Laravel\Forge\Resources\Deployment
     */
    public function deployment($organizationSlug, $serverId, $siteId, $deploymentId)
    {
        return new Deployment(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/deployments/{$deploymentId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a new deployment.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Deployment
     */
    public function createDeployment($organizationSlug, $serverId, $siteId, array $data = [])
    {
        $deployment = $this->post(
            "orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/deployments",
            $data
        )['data'] ?? [];

        return new Deployment(
            $deployment + ['organization_id' => $organizationSlug, 'server_id' => $serverId, 'site_id' => $siteId],
            $this
        );
    }

    /**
     * Get the deployment status for a site.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return array
     */
    public function deploymentStatus($organizationSlug, $serverId, $siteId)
    {
        return $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/deployments/status")['data'] ?? [];
    }

    /**
     * Disable quick deploy for a site.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return void
     */
    public function disableQuickDeploy($organizationSlug, $serverId, $siteId)
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/deployments/status");
    }

    /**
     * Get the deployment script for a site.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return string
     */
    public function deploymentScript($organizationSlug, $serverId, $siteId)
    {
        $response = $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/deployments/script");

        return $response['data']['script'] ?? $response['script'] ?? '';
    }

    /**
     * Update the deployment script for a site.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return void
     */
    public function updateDeploymentScript($organizationSlug, $serverId, $siteId, array $data)
    {
        $this->put("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/deployments/script", $data);
    }

    /**
     * Get the deployment trigger URL for a site.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return string
     */
    public function deploymentTriggerUrl($organizationSlug, $serverId, $siteId)
    {
        $response = $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/deployments/deploy-hook");

        return $response['data']['url'] ?? $response['url'] ?? '';
    }

    /**
     * Update the deployment trigger URL for a site.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return void
     */
    public function updateDeploymentTriggerUrl($organizationSlug, $serverId, $siteId, array $data)
    {
        $this->put("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/deployments/deploy-hook", $data);
    }

    /**
     * Enable push to deploy for a site.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return void
     */
    public function enablePushToDeploy($organizationSlug, $serverId, $siteId, array $data)
    {
        $this->post("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/deployments/push-to-deploy", $data);
    }

    /**
     * Disable push to deploy for a site (delete the push to deploy configuration).
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return void
     */
    public function disablePushToDeploy($organizationSlug, $serverId, $siteId)
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/deployments/push-to-deploy");
    }

    /**
     * Get the deployment log for a deployment.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $deploymentId
     * @return string
     */
    public function deploymentLog($organizationSlug, $serverId, $siteId, $deploymentId)
    {
        $response = $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/deployments/{$deploymentId}/log");

        return $response['data']['output'] ?? $response['output'] ?? '';
    }
}
