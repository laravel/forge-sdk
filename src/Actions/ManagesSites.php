<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Certificate;
use Laravel\Forge\Resources\Domain;
use Laravel\Forge\Resources\Heartbeat;
use Laravel\Forge\Resources\Site;
use Laravel\Forge\Resources\Worker;

trait ManagesSites
{
    /**
     * Get the collection of all sites.
     *
     * @return \Laravel\Forge\Resources\Site[]
     */
    public function sites()
    {
        return $this->transformCollection(
            $this->get('sites')['data'] ?? [],
            Site::class
        );
    }

    /**
     * Get the collection of sites for an organization.
     *
     * @param  string  $organizationSlug
     * @return \Laravel\Forge\Resources\Site[]
     */
    public function organizationSites($organizationSlug)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/sites")['data'] ?? [],
            Site::class,
            ['organization_id' => $organizationSlug]
        );
    }

    /**
     * Get a site instance.
     *
     * @param  string  $organizationSlug
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Site
     */
    public function organizationSite($organizationSlug, $siteId)
    {
        return new Site(
            $this->get("orgs/{$organizationSlug}/sites/{$siteId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Get the collection of sites for a server.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return \Laravel\Forge\Resources\Site[]
     */
    public function serverSites($organizationSlug, $serverId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites")['data'] ?? [],
            Site::class,
            ['organization_id' => $organizationSlug, 'server_id' => $serverId]
        );
    }

    /**
     * Create a new site.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return \Laravel\Forge\Resources\Site
     */
    public function createSite($organizationSlug, $serverId, array $data)
    {
        $site = $this->post("orgs/{$organizationSlug}/servers/{$serverId}/sites", $data)['data'] ?? [];

        return new Site($site + ['organization_id' => $organizationSlug, 'server_id' => $serverId], $this);
    }

    /**
     * Update a site.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Site
     */
    public function updateSite($organizationSlug, $serverId, $siteId, array $data)
    {
        $site = $this->put(
            "orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}",
            $data
        )['data'] ?? [];

        return new Site($site, $this);
    }

    /**
     * Delete the given site.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return void
     */
    public function deleteSite($organizationSlug, $serverId, $siteId)
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}");
    }

    /**
     * Get the collection of domains for a site.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Domain[]
     */
    public function domains($organizationSlug, $serverId, $siteId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/domains")['data'] ?? [],
            Domain::class,
            ['organization_id' => $organizationSlug, 'server_id' => $serverId, 'site_id' => $siteId]
        );
    }

    /**
     * Create a new domain.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Domain
     */
    public function createDomain($organizationSlug, $serverId, $siteId, array $data)
    {
        $domain = $this->post(
            "orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/domains",
            $data
        )['data'] ?? [];

        return new Domain($domain + ['organization_id' => $organizationSlug, 'server_id' => $serverId, 'site_id' => $siteId], $this);
    }

    /**
     * Get a domain instance.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $domainId
     * @return \Laravel\Forge\Resources\Domain
     */
    public function domain($organizationSlug, $serverId, $siteId, $domainId)
    {
        return new Domain(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/domains/{$domainId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Update a domain.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $domainId
     * @return \Laravel\Forge\Resources\Domain
     */
    public function updateDomain($organizationSlug, $serverId, $siteId, $domainId, array $data)
    {
        $domain = $this->patch(
            "orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/domains/{$domainId}",
            $data
        )['data'] ?? [];

        return new Domain($domain, $this);
    }

    /**
     * Delete the given domain.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $domainId
     * @return void
     */
    public function deleteDomain($organizationSlug, $serverId, $siteId, $domainId)
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/domains/{$domainId}");
    }

    /**
     * Get domain DNS configurations.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $domainId
     * @return array
     */
    public function domainConfigurations($organizationSlug, $serverId, $siteId, $domainId)
    {
        return $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/domains/{$domainId}/configurations")['data'] ?? [];
    }

    /**
     * Create a domain action.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $domainId
     * @return mixed
     */
    public function createDomainAction($organizationSlug, $serverId, $siteId, $domainId, array $data)
    {
        return $this->post(
            "orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/domains/{$domainId}/actions",
            $data
        );
    }

    /**
     * Get a domain certificate.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $domainId
     * @return \Laravel\Forge\Resources\Certificate
     */
    public function domainCertificate($organizationSlug, $serverId, $siteId, $domainId)
    {
        return new Certificate(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/domains/{$domainId}/certificate")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a domain certificate.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $domainId
     * @return \Laravel\Forge\Resources\Certificate
     */
    public function createDomainCertificate($organizationSlug, $serverId, $siteId, $domainId, array $data)
    {
        $certificate = $this->post(
            "orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/domains/{$domainId}/certificate",
            $data
        )['data'] ?? [];

        return new Certificate($certificate + ['organization_id' => $organizationSlug, 'server_id' => $serverId, 'site_id' => $siteId], $this);
    }

    /**
     * Delete the given domain certificate.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $domainId
     * @return void
     */
    public function deleteDomainCertificate($organizationSlug, $serverId, $siteId, $domainId)
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/domains/{$domainId}/certificate");
    }

    /**
     * Create a domain certificate action.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $domainId
     * @return mixed
     */
    public function createDomainCertificateAction($organizationSlug, $serverId, $siteId, $domainId, array $data)
    {
        return $this->post(
            "orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/domains/{$domainId}/certificate/actions",
            $data
        );
    }

    /**
     * Get the collection of workers for a site.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Worker[]
     */
    public function workers($organizationSlug, $serverId, $siteId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/workers")['data'] ?? [],
            Worker::class,
            ['organization_id' => $organizationSlug, 'server_id' => $serverId, 'site_id' => $siteId]
        );
    }

    /**
     * Create a new worker.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Worker
     */
    public function createWorker($organizationSlug, $serverId, $siteId, array $data)
    {
        $worker = $this->post(
            "orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/workers",
            $data
        )['data'] ?? [];

        return new Worker($worker + ['organization_id' => $organizationSlug, 'server_id' => $serverId, 'site_id' => $siteId], $this);
    }

    /**
     * Get a worker instance.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $workerId
     * @return \Laravel\Forge\Resources\Worker
     */
    public function worker($organizationSlug, $serverId, $siteId, $workerId)
    {
        return new Worker(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/workers/{$workerId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Delete the given worker.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $workerId
     * @return void
     */
    public function deleteWorker($organizationSlug, $serverId, $siteId, $workerId)
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/workers/{$workerId}");
    }

    /**
     * Create a worker action.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $workerId
     * @return mixed
     */
    public function createWorkerAction($organizationSlug, $serverId, $siteId, $workerId, array $data)
    {
        return $this->post(
            "orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/workers/{$workerId}/actions",
            $data
        );
    }

    /**
     * Get the site environment file.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return string
     */
    public function siteEnvironment($organizationSlug, $serverId, $siteId)
    {
        $response = $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/environment");

        return $response['data']['content'] ?? $response['content'] ?? '';
    }

    /**
     * Update the site environment file.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $content
     * @return void
     */
    public function updateSiteEnvironment($organizationSlug, $serverId, $siteId, $content)
    {
        $this->put("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/environment", [
            'content' => $content,
        ]);
    }

    /**
     * Get the site Nginx configuration.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return string
     */
    public function siteNginx($organizationSlug, $serverId, $siteId)
    {
        $response = $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/nginx");

        return $response['data']['content'] ?? $response['content'] ?? '';
    }

    /**
     * Update the site Nginx configuration.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $content
     * @return void
     */
    public function updateSiteNginx($organizationSlug, $serverId, $siteId, $content)
    {
        $this->put("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/nginx", [
            'content' => $content,
        ]);
    }

    /**
     * Get the site PHP version.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return array
     */
    public function sitePhp($organizationSlug, $serverId, $siteId)
    {
        return $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/php")['data'] ?? [];
    }

    /**
     * Update the site PHP version.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return void
     */
    public function updateSitePhp($organizationSlug, $serverId, $siteId, array $data)
    {
        $this->put("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/php", $data);
    }

    /**
     * Get the Nginx access log.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return string
     */
    public function siteNginxAccessLog($organizationSlug, $serverId, $siteId)
    {
        $response = $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/logs/nginx-access");

        return $response['data']['content'] ?? $response['content'] ?? '';
    }

    /**
     * Delete the Nginx access log.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return void
     */
    public function deleteSiteNginxAccessLog($organizationSlug, $serverId, $siteId)
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/logs/nginx-access");
    }

    /**
     * Get the Nginx error log.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return string
     */
    public function siteNginxErrorLog($organizationSlug, $serverId, $siteId)
    {
        $response = $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/logs/nginx-error");

        return $response['data']['content'] ?? $response['content'] ?? '';
    }

    /**
     * Delete the Nginx error log.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return void
     */
    public function deleteSiteNginxErrorLog($organizationSlug, $serverId, $siteId)
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/logs/nginx-error");
    }

    /**
     * Get the application log.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return string
     */
    public function siteApplicationLog($organizationSlug, $serverId, $siteId)
    {
        $response = $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/logs/application");

        return $response['data']['content'] ?? $response['content'] ?? '';
    }

    /**
     * Delete the application log.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return void
     */
    public function deleteSiteApplicationLog($organizationSlug, $serverId, $siteId)
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/logs/application");
    }

    /**
     * Get the collection of heartbeats for a site.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Heartbeat[]
     */
    public function heartbeats($organizationSlug, $serverId, $siteId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/heartbeats")['data'] ?? [],
            Heartbeat::class,
            ['organization_id' => $organizationSlug, 'server_id' => $serverId, 'site_id' => $siteId]
        );
    }

    /**
     * Create a new heartbeat.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Heartbeat
     */
    public function createHeartbeat($organizationSlug, $serverId, $siteId, array $data)
    {
        $heartbeat = $this->post(
            "orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/heartbeats",
            $data
        )['data'] ?? [];

        return new Heartbeat($heartbeat + ['organization_id' => $organizationSlug, 'server_id' => $serverId, 'site_id' => $siteId], $this);
    }

    /**
     * Get a heartbeat instance.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $heartbeatId
     * @return \Laravel\Forge\Resources\Heartbeat
     */
    public function heartbeat($organizationSlug, $serverId, $siteId, $heartbeatId)
    {
        return new Heartbeat(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/heartbeats/{$heartbeatId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Update a heartbeat.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $heartbeatId
     * @return \Laravel\Forge\Resources\Heartbeat
     */
    public function updateHeartbeat($organizationSlug, $serverId, $siteId, $heartbeatId, array $data)
    {
        $heartbeat = $this->put(
            "orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/heartbeats/{$heartbeatId}",
            $data
        )['data'] ?? [];

        return new Heartbeat($heartbeat, $this);
    }

    /**
     * Delete the given heartbeat.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $heartbeatId
     * @return void
     */
    public function deleteHeartbeat($organizationSlug, $serverId, $siteId, $heartbeatId)
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/heartbeats/{$heartbeatId}");
    }

    /**
     * Get the nginx configuration for a domain.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $domainId
     * @return string
     */
    public function domainNginxConfig($organizationSlug, $serverId, $siteId, $domainId)
    {
        $response = $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/domains/{$domainId}/nginx");

        return $response['data']['content'] ?? $response['content'] ?? '';
    }

    /**
     * Update the nginx configuration for a domain.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $domainId
     * @return void
     */
    public function updateDomainNginxConfig($organizationSlug, $serverId, $siteId, $domainId, $content)
    {
        $this->put("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/domains/{$domainId}/nginx", ['content' => $content]);
    }

    /**
     * Get the health check configuration for a site.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return array
     */
    public function siteHealthcheck($organizationSlug, $serverId, $siteId)
    {
        return $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/healthcheck")['data'] ?? [];
    }

    /**
     * Update the health check configuration for a site.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return void
     */
    public function updateSiteHealthcheck($organizationSlug, $serverId, $siteId, array $data)
    {
        $this->put("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/healthcheck", $data);
    }

    /**
     * Get composer credentials for a site.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return array
     */
    public function composerCredentials($organizationSlug, $serverId, $siteId)
    {
        return $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/composer/credentials")['data'] ?? [];
    }

    /**
     * Create a composer credential for a site.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return array
     */
    public function createComposerCredential($organizationSlug, $serverId, $siteId, array $data)
    {
        return $this->post("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/composer/credentials", $data)['data'] ?? [];
    }

    /**
     * Get a composer credential for a site.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $repository
     * @return array
     */
    public function composerCredential($organizationSlug, $serverId, $siteId, $repository)
    {
        return $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/composer/credentials/{$repository}")['data'] ?? [];
    }

    /**
     * Update a composer credential for a site.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $repository
     * @return array
     */
    public function updateComposerCredential($organizationSlug, $serverId, $siteId, $repository, array $data)
    {
        return $this->put("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/composer/credentials/{$repository}", $data)['data'] ?? [];
    }

    /**
     * Delete a composer credential for a site.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $repository
     * @return void
     */
    public function deleteComposerCredential($organizationSlug, $serverId, $siteId, $repository)
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/composer/credentials/{$repository}");
    }

    /**
     * Get load balancing nodes for a site.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return array
     */
    public function loadBalancingNodes($organizationSlug, $serverId, $siteId)
    {
        return $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/load-balancing-nodes")['data'] ?? [];
    }

    /**
     * Update load balancing nodes for a site.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return array
     */
    public function updateLoadBalancingNodes($organizationSlug, $serverId, $siteId, array $data)
    {
        return $this->put("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/load-balancing-nodes", $data)['data'] ?? [];
    }
}
