<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

use Laravel\Forge\CursorPaginator;

class Site extends Resource
{
    /**
     * The slug of the organization.
     */
    public string $organizationSlug;

    /**
     * The id of the site.
     */
    public ?int $id = null;

    /**
     * The id of the server.
     */
    public ?int $serverId = null;

    /**
     * The name of the site.
     */
    public ?string $name = null;

    /**
     * The aliases of the site.
     */
    public array $aliases = [];

    /**
     * Determine if the site allows Wildcard Sub-Domains.
     */
    public ?bool $wildcards = null;

    /**
     * The status of the site.
     */
    public ?string $status = null;

    /**
     * The deployment git repository.
     */
    public mixed $repository = null;

    /**
     * Determine if "Quick Deploy" is enabled for the site.
     */
    public ?bool $quickDeploy = null;

    /**
     * The status of the deployment.
     */
    public ?string $deploymentStatus = null;

    /**
     * The date/time the site was created.
     */
    public ?string $createdAt = null;

    /**
     * The PHP Version of the site, if any.
     */
    public ?string $phpVersion = null;

    /**
     * The URL of the site.
     */
    public ?string $url = null;

    /**
     * The user the site runs as.
     */
    public ?string $user = null;

    /**
     * Whether the site uses HTTPS.
     */
    public ?bool $https = null;

    /**
     * The web directory of the site.
     */
    public ?string $webDirectory = null;

    /**
     * The root directory of the site.
     */
    public ?string $rootDirectory = null;

    /**
     * Whether the site is isolated.
     */
    public ?bool $isolated = null;

    /**
     * The shared paths of the site.
     */
    public array $sharedPaths = [];

    /**
     * The database associated with the site.
     */
    public ?string $database = null;

    /**
     * The maintenance mode status of the site.
     */
    public ?array $maintenanceMode = null;

    /**
     * Whether zero downtime deployments are enabled.
     */
    public ?bool $zeroDowntimeDeployments = null;

    /**
     * The deployment script of the site.
     */
    public ?string $deploymentScript = null;

    /**
     * The application type of the site.
     */
    public ?string $appType = null;

    /**
     * Whether the site uses Envoyer.
     */
    public ?bool $usesEnvoyer = null;

    /**
     * The deployment URL for the site.
     */
    public ?string $deploymentUrl = null;

    /**
     * The healthcheck URL for the site.
     */
    public ?string $healthcheckUrl = null;

    /**
     * The date/time the site was last updated.
     */
    public ?string $updatedAt = null;

    /**
     * Delete the given site.
     */
    public function delete(): void
    {
        $this->forge->deleteSite($this->organizationSlug, $this->serverId, $this->id);
    }

    /**
     * Get the content of the site's deployment script.
     */
    public function getDeploymentScript(): string
    {
        return $this->forge->deploymentScript($this->organizationSlug, $this->serverId, $this->id);
    }

    /**
     * Update the content of the site's deployment script.
     */
    public function updateDeploymentScript(array $data): static
    {
        $this->forge->updateDeploymentScript($this->organizationSlug, $this->serverId, $this->id, $data);

        return $this;
    }

    /**
     * Disable "Quick Deploy" for the given site.
     */
    public function disableQuickDeploy(): void
    {
        $this->forge->disableQuickDeploy($this->organizationSlug, $this->serverId, $this->id);
    }

    /**
     * Deploy the given site.
     */
    public function deploySite(): Deployment
    {
        return $this->forge->createDeployment($this->organizationSlug, $this->serverId, $this->id);
    }

    /**
     * Get the deployments history of the site.
     */
    public function getDeploymentHistory(): CursorPaginator
    {
        return $this->forge->deployments($this->organizationSlug, $this->serverId, $this->id);
    }

    /**
     * Get a single deployment from the deployment history of a site.
     */
    public function getDeploymentHistoryDeployment(int $deploymentId): Deployment
    {
        return $this->forge->deployment($this->organizationSlug, $this->serverId, $this->id, $deploymentId);
    }

    /**
     * Get the output for a deployment of the site.
     */
    public function getDeploymentHistoryOutput(int $deploymentId): string
    {
        return $this->forge->deploymentLog($this->organizationSlug, $this->serverId, $this->id, $deploymentId);
    }

}
