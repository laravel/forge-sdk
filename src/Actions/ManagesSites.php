<?php

declare(strict_types=1);

namespace Laravel\Forge\Actions;

use Laravel\Forge\CursorPaginator;
use Laravel\Forge\Resources\Certificate;
use Laravel\Forge\Resources\Domain;
use Laravel\Forge\Resources\Heartbeat;
use Laravel\Forge\Resources\Site;

trait ManagesSites
{
    /**
     * Get the collection of all sites.
     */
    public function sites(): CursorPaginator
    {
        return $this->paginatedCollection(
            'sites',
            Site::class,
        );
    }

    /**
     * Get the collection of sites for an organization.
     */
    public function organizationSites(string $organizationSlug): CursorPaginator
    {
        return $this->paginatedCollection(
            "orgs/{$organizationSlug}/sites",
            Site::class,
            $organizationSlug,
        );
    }

    /**
     * Get a site instance.
     */
    public function organizationSite(string $organizationSlug, int $siteId): Site
    {
        return $this->newResource(
            Site::class,
            $this->get("orgs/{$organizationSlug}/sites/{$siteId}")['data'] ?? [],
            $organizationSlug,
        );
    }

    /**
     * Get the collection of sites for a server.
     */
    public function serverSites(string $organizationSlug, int $serverId): CursorPaginator
    {
        return $this->paginatedCollection(
            "orgs/{$organizationSlug}/servers/{$serverId}/sites",
            Site::class,
            $organizationSlug,
            $serverId,
        );
    }

    /**
     * Create a new site.
     */
    public function createSite(string $organizationSlug, int $serverId, array $data): Site
    {
        return $this->newResource(
            Site::class,
            $this->post("orgs/{$organizationSlug}/servers/{$serverId}/sites", $data)['data'] ?? [],
            $organizationSlug,
            $serverId,
        );
    }

    /**
     * Update a site.
     */
    public function updateSite(string $organizationSlug, int $serverId, int $siteId, array $data): Site
    {
        return $this->newResource(
            Site::class,
            $this->put("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}", $data)['data'] ?? [],
            $organizationSlug,
            $serverId,
        );
    }

    /**
     * Create a new load balancer site.
     */
    public function createBalancer(string $organizationSlug, int $serverId, array $data): Site
    {
        return $this->newResource(
            Site::class,
            $this->post("orgs/{$organizationSlug}/servers/{$serverId}/sites/balancer", $data)['data'] ?? [],
            $organizationSlug,
            $serverId,
        );
    }

    /**
     * Delete the given site.
     */
    public function deleteSite(string $organizationSlug, int $serverId, int $siteId): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}");
    }

    /**
     * Get the collection of domains for a site.
     */
    public function domains(string $organizationSlug, int $serverId, int $siteId): CursorPaginator
    {
        return $this->paginatedCollection(
            "orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/domains",
            Domain::class,
            $organizationSlug,
            $serverId,
            $siteId,
        );
    }

    /**
     * Create a new domain.
     */
    public function createDomain(string $organizationSlug, int $serverId, int $siteId, array $data): Domain
    {
        return $this->newResource(
            Domain::class,
            $this->post("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/domains", $data)['data'] ?? [],
            $organizationSlug,
            $serverId,
            $siteId,
        );
    }

    /**
     * Get a domain instance.
     */
    public function domain(string $organizationSlug, int $serverId, int $siteId, int $domainId): Domain
    {
        return $this->newResource(
            Domain::class,
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/domains/{$domainId}")['data'] ?? [],
            $organizationSlug,
            $serverId,
            $siteId,
        );
    }

    /**
     * Update a domain.
     */
    public function updateDomain(string $organizationSlug, int $serverId, int $siteId, int $domainId, array $data): Domain
    {
        return $this->newResource(
            Domain::class,
            $this->patch("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/domains/{$domainId}", $data)['data'] ?? [],
            $organizationSlug,
            $serverId,
            $siteId,
        );
    }

    /**
     * Delete the given domain.
     */
    public function deleteDomain(string $organizationSlug, int $serverId, int $siteId, int $domainId): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/domains/{$domainId}");
    }

    /**
     * Get domain DNS configurations.
     */
    public function domainConfigurations(string $organizationSlug, int $serverId, int $siteId, int $domainId): array
    {
        return $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/domains/{$domainId}/configurations")['data'] ?? [];
    }

    /**
     * Create a domain action.
     */
    public function createDomainAction(string $organizationSlug, int $serverId, int $siteId, int $domainId, array $data): array
    {
        $response = $this->post(
            "orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/domains/{$domainId}/actions",
            $data
        );

        return is_array($response) ? $response : [];
    }

    /**
     * Get a domain certificate.
     */
    public function domainCertificate(string $organizationSlug, int $serverId, int $siteId, int $domainId): Certificate
    {
        return $this->newResource(
            Certificate::class,
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/domains/{$domainId}/certificate")['data'] ?? [],
            $organizationSlug,
            $serverId,
            $siteId,
        );
    }

    /**
     * Create a domain certificate.
     */
    public function createDomainCertificate(string $organizationSlug, int $serverId, int $siteId, int $domainId, array $data): Certificate
    {
        return $this->newResource(
            Certificate::class,
            $this->post("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/domains/{$domainId}/certificate", $data)['data'] ?? [],
            $organizationSlug,
            $serverId,
            $siteId,
        );
    }

    /**
     * Delete the given domain certificate.
     */
    public function deleteDomainCertificate(string $organizationSlug, int $serverId, int $siteId, int $domainId): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/domains/{$domainId}/certificate");
    }

    /**
     * Create a domain certificate action.
     */
    public function createDomainCertificateAction(string $organizationSlug, int $serverId, int $siteId, int $domainId, array $data): array
    {
        $response = $this->post(
            "orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/domains/{$domainId}/certificate/actions",
            $data
        );

        return is_array($response) ? $response : [];
    }

    /**
     * Get the site environment file.
     */
    public function siteEnvironment(string $organizationSlug, int $serverId, int $siteId): string
    {
        $response = $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/environment");

        return $response['data']['attributes']['content'] ?? '';
    }

    /**
     * Update the site environment file.
     */
    public function updateSiteEnvironment(string $organizationSlug, int $serverId, int $siteId, string $content): void
    {
        $this->put("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/environment", [
            'content' => $content,
        ]);
    }

    /**
     * Get the site Nginx configuration.
     */
    public function siteNginx(string $organizationSlug, int $serverId, int $siteId): string
    {
        $response = $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/nginx");

        return $response['data']['attributes']['content'] ?? '';
    }

    /**
     * Update the site Nginx configuration.
     */
    public function updateSiteNginx(string $organizationSlug, int $serverId, int $siteId, string $content): void
    {
        $this->put("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/nginx", [
            'content' => $content,
        ]);
    }

    /**
     * Get the Nginx access log.
     */
    public function siteNginxAccessLog(string $organizationSlug, int $serverId, int $siteId): string
    {
        $response = $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/logs/nginx-access");

        return $response['data']['attributes']['content'] ?? '';
    }

    /**
     * Delete the Nginx access log.
     */
    public function deleteSiteNginxAccessLog(string $organizationSlug, int $serverId, int $siteId): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/logs/nginx-access");
    }

    /**
     * Get the Nginx error log.
     */
    public function siteNginxErrorLog(string $organizationSlug, int $serverId, int $siteId): string
    {
        $response = $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/logs/nginx-error");

        return $response['data']['attributes']['content'] ?? '';
    }

    /**
     * Delete the Nginx error log.
     */
    public function deleteSiteNginxErrorLog(string $organizationSlug, int $serverId, int $siteId): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/logs/nginx-error");
    }

    /**
     * Get the application log.
     */
    public function siteApplicationLog(string $organizationSlug, int $serverId, int $siteId): string
    {
        $response = $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/logs/application");

        return $response['data']['attributes']['content'] ?? '';
    }

    /**
     * Delete the application log.
     */
    public function deleteSiteApplicationLog(string $organizationSlug, int $serverId, int $siteId): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/logs/application");
    }

    /**
     * Get the collection of heartbeats for a site.
     */
    public function heartbeats(string $organizationSlug, int $serverId, int $siteId): CursorPaginator
    {
        return $this->paginatedCollection(
            "orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/heartbeats",
            Heartbeat::class,
            $organizationSlug,
            $serverId,
            $siteId,
        );
    }

    /**
     * Create a new heartbeat.
     */
    public function createHeartbeat(string $organizationSlug, int $serverId, int $siteId, array $data): Heartbeat
    {
        return $this->newResource(
            Heartbeat::class,
            $this->post("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/heartbeats", $data)['data'] ?? [],
            $organizationSlug,
            $serverId,
            $siteId,
        );
    }

    /**
     * Get a heartbeat instance.
     */
    public function heartbeat(string $organizationSlug, int $serverId, int $siteId, int $heartbeatId): Heartbeat
    {
        return $this->newResource(
            Heartbeat::class,
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/heartbeats/{$heartbeatId}")['data'] ?? [],
            $organizationSlug,
            $serverId,
            $siteId,
        );
    }

    /**
     * Update a heartbeat.
     */
    public function updateHeartbeat(string $organizationSlug, int $serverId, int $siteId, int $heartbeatId, array $data): Heartbeat
    {
        return $this->newResource(
            Heartbeat::class,
            $this->put("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/heartbeats/{$heartbeatId}", $data)['data'] ?? [],
            $organizationSlug,
            $serverId,
            $siteId,
        );
    }

    /**
     * Delete the given heartbeat.
     */
    public function deleteHeartbeat(string $organizationSlug, int $serverId, int $siteId, int $heartbeatId): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/heartbeats/{$heartbeatId}");
    }

    /**
     * Get the nginx configuration for a domain.
     */
    public function domainNginxConfig(string $organizationSlug, int $serverId, int $siteId, int $domainId): string
    {
        $response = $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/domains/{$domainId}/nginx");

        return $response['data']['attributes']['content'] ?? '';
    }

    /**
     * Update the nginx configuration for a domain.
     */
    public function updateDomainNginxConfig(string $organizationSlug, int $serverId, int $siteId, int $domainId, string $content): void
    {
        $this->put("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/domains/{$domainId}/nginx", ['content' => $content]);
    }

    /**
     * Get the health check configuration for a site.
     */
    public function siteHealthcheck(string $organizationSlug, int $serverId, int $siteId): array
    {
        return $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/healthcheck")['data'] ?? [];
    }

    /**
     * Update the health check configuration for a site.
     */
    public function updateSiteHealthcheck(string $organizationSlug, int $serverId, int $siteId, array $data): void
    {
        $this->put("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/healthcheck", $data);
    }

    /**
     * Get composer credentials for a site.
     */
    public function composerCredentials(string $organizationSlug, int $serverId, int $siteId): array
    {
        return $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/composer/credentials")['data'] ?? [];
    }

    /**
     * Create a composer credential for a site.
     */
    public function createComposerCredential(string $organizationSlug, int $serverId, int $siteId, array $data): array
    {
        return $this->post("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/composer/credentials", $data)['data'] ?? [];
    }

    /**
     * Get a composer credential for a site.
     */
    public function composerCredential(string $organizationSlug, int $serverId, int $siteId, string $repository): array
    {
        return $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/composer/credentials/{$repository}")['data'] ?? [];
    }

    /**
     * Update a composer credential for a site.
     */
    public function updateComposerCredential(string $organizationSlug, int $serverId, int $siteId, string $repository, array $data): array
    {
        return $this->put("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/composer/credentials/{$repository}", $data)['data'] ?? [];
    }

    /**
     * Delete a composer credential for a site.
     */
    public function deleteComposerCredential(string $organizationSlug, int $serverId, int $siteId, string $repository): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/composer/credentials/{$repository}");
    }

    /**
     * Get npm credentials for a site.
     */
    public function npmCredentials(string $organizationSlug, int $serverId, int $siteId): array
    {
        return $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/npm/credentials")['data'] ?? [];
    }

    /**
     * Create an npm credential for a site.
     */
    public function createNpmCredential(string $organizationSlug, int $serverId, int $siteId, array $data): array
    {
        return $this->post("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/npm/credentials", $data)['data'] ?? [];
    }

    /**
     * Get an npm credential for a site.
     */
    public function npmCredential(string $organizationSlug, int $serverId, int $siteId, string $registry): array
    {
        return $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/npm/credentials/{$registry}")['data'] ?? [];
    }

    /**
     * Update an npm credential for a site.
     */
    public function updateNpmCredential(string $organizationSlug, int $serverId, int $siteId, string $registry, array $data): array
    {
        return $this->put("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/npm/credentials/{$registry}", $data)['data'] ?? [];
    }

    /**
     * Delete an npm credential for a site.
     */
    public function deleteNpmCredential(string $organizationSlug, int $serverId, int $siteId, string $registry): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/npm/credentials/{$registry}");
    }

    /**
     * Get load balancing nodes for a site.
     */
    public function loadBalancingNodes(string $organizationSlug, int $serverId, int $siteId): array
    {
        return $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/load-balancing-nodes")['data'] ?? [];
    }

    /**
     * Update load balancing nodes for a site.
     */
    public function updateLoadBalancingNodes(string $organizationSlug, int $serverId, int $siteId, array $data): array
    {
        return $this->put("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/load-balancing-nodes", $data)['data'] ?? [];
    }
}
