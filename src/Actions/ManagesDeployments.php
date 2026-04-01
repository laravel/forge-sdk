<?php

declare(strict_types=1);

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Deployment;
use Laravel\Forge\Resources\Webhook;

trait ManagesDeployments
{
    /**
     * Get the collection of webhooks for a site.
     *
     * @return Webhook[]
     */
    public function webhooks(string $organizationSlug, int $serverId, int $siteId): array
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/webhooks")['data'] ?? [],
            Webhook::class,
            $organizationSlug,
            $serverId,
            $siteId,
        );
    }

    /**
     * Get a webhook instance.
     */
    public function webhook(string $organizationSlug, int $serverId, int $siteId, int $webhookId): Webhook
    {
        return $this->newResource(
            Webhook::class,
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/webhooks/{$webhookId}")['data'] ?? [],
            $organizationSlug,
            $serverId,
            $siteId,
        );
    }

    /**
     * Create a new webhook.
     */
    public function createWebhook(string $organizationSlug, int $serverId, int $siteId, array $data): Webhook
    {
        return $this->newResource(
            Webhook::class,
            $this->post("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/webhooks", $data)['data'] ?? [],
            $organizationSlug,
            $serverId,
            $siteId,
        );
    }

    /**
     * Delete the given webhook.
     */
    public function deleteWebhook(string $organizationSlug, int $serverId, int $siteId, int $webhookId): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/webhooks/{$webhookId}");
    }

    /**
     * Get the collection of deployments for a site.
     *
     * @return Deployment[]
     */
    public function deployments(string $organizationSlug, int $serverId, int $siteId): array
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/deployments")['data'] ?? [],
            Deployment::class,
            $organizationSlug,
            $serverId,
            $siteId,
        );
    }

    /**
     * Get a deployment instance.
     */
    public function deployment(string $organizationSlug, int $serverId, int $siteId, int $deploymentId): Deployment
    {
        return $this->newResource(
            Deployment::class,
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/deployments/{$deploymentId}")['data'] ?? [],
            $organizationSlug,
            $serverId,
            $siteId,
        );
    }

    /**
     * Create a new deployment.
     */
    public function createDeployment(string $organizationSlug, int $serverId, int $siteId, array $data = []): Deployment
    {
        return $this->newResource(
            Deployment::class,
            $this->post("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/deployments", $data)['data'] ?? [],
            $organizationSlug,
            $serverId,
            $siteId,
        );
    }

    /**
     * Get the deployment status for a site.
     */
    public function deploymentStatus(string $organizationSlug, int $serverId, int $siteId): array
    {
        return $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/deployments/status")['data'] ?? [];
    }

    /**
     * Disable quick deploy for a site.
     */
    public function disableQuickDeploy(string $organizationSlug, int $serverId, int $siteId): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/deployments/status");
    }

    /**
     * Get the deployment script for a site.
     */
    public function deploymentScript(string $organizationSlug, int $serverId, int $siteId): string
    {
        $response = $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/deployments/script");

        return $response['data']['attributes']['content'] ?? '';
    }

    /**
     * Update the deployment script for a site.
     */
    public function updateDeploymentScript(string $organizationSlug, int $serverId, int $siteId, array $data): void
    {
        $this->put("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/deployments/script", $data);
    }

    /**
     * Get the deployment trigger URL for a site.
     */
    public function deploymentTriggerUrl(string $organizationSlug, int $serverId, int $siteId): string
    {
        $response = $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/deployments/deploy-hook");

        return $response['data']['attributes']['url'] ?? '';
    }

    /**
     * Update the deployment trigger URL for a site.
     */
    public function updateDeploymentTriggerUrl(string $organizationSlug, int $serverId, int $siteId, array $data): void
    {
        $this->put("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/deployments/deploy-hook", $data);
    }

    /**
     * Enable push to deploy for a site.
     */
    public function enablePushToDeploy(string $organizationSlug, int $serverId, int $siteId, array $data): void
    {
        $this->post("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/deployments/push-to-deploy", $data);
    }

    /**
     * Disable push to deploy for a site (delete the push to deploy configuration).
     */
    public function disablePushToDeploy(string $organizationSlug, int $serverId, int $siteId): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/deployments/push-to-deploy");
    }

    /**
     * Get the deployment log for a deployment.
     */
    public function deploymentLog(string $organizationSlug, int $serverId, int $siteId, int $deploymentId): string
    {
        $response = $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/deployments/{$deploymentId}/log");

        return $response['data']['attributes']['output'] ?? '';
    }
}
