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
     * @param  string  $organizationId
     * @return \Laravel\Forge\Resources\Site[]
     */
    public function organizationSites($organizationId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationId}/sites")['data'] ?? [],
            Site::class,
            ['organization_id' => $organizationId]
        );
    }

    /**
     * Get a site instance.
     *
     * @param  string  $organizationId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Site
     */
    public function organizationSite($organizationId, $siteId)
    {
        return new Site(
            $this->get("orgs/{$organizationId}/sites/{$siteId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Get the collection of sites for a server.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @return \Laravel\Forge\Resources\Site[]
     */
    public function serverSites($organizationId, $serverId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/sites")['data'] ?? [],
            Site::class,
            ['organization_id' => $organizationId, 'server_id' => $serverId]
        );
    }

    /**
     * Create a new site.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  array  $data
     * @return \Laravel\Forge\Resources\Site
     */
    public function createSite($organizationId, $serverId, array $data)
    {
        $site = $this->post("orgs/{$organizationId}/servers/{$serverId}/sites", $data)['data'] ?? [];

        return new Site($site + ['organization_id' => $organizationId, 'server_id' => $serverId], $this);
    }

    /**
     * Update a site.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  array  $data
     * @return \Laravel\Forge\Resources\Site
     */
    public function updateSite($organizationId, $serverId, $siteId, array $data)
    {
        $site = $this->put(
            "orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}",
            $data
        )['data'] ?? [];

        return new Site($site, $this);
    }

    /**
     * Delete the given site.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return void
     */
    public function deleteSite($organizationId, $serverId, $siteId)
    {
        $this->delete("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}");
    }

    /**
     * Get the collection of domains for a site.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Domain[]
     */
    public function domains($organizationId, $serverId, $siteId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/domains")['data'] ?? [],
            Domain::class,
            ['organization_id' => $organizationId, 'server_id' => $serverId, 'site_id' => $siteId]
        );
    }

    /**
     * Create a new domain.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  array  $data
     * @return \Laravel\Forge\Resources\Domain
     */
    public function createDomain($organizationId, $serverId, $siteId, array $data)
    {
        $domain = $this->post(
            "orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/domains",
            $data
        )['data'] ?? [];

        return new Domain($domain + ['organization_id' => $organizationId, 'server_id' => $serverId, 'site_id' => $siteId], $this);
    }

    /**
     * Get a domain instance.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $domainId
     * @return \Laravel\Forge\Resources\Domain
     */
    public function domain($organizationId, $serverId, $siteId, $domainId)
    {
        return new Domain(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/domains/{$domainId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Update a domain.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $domainId
     * @param  array  $data
     * @return \Laravel\Forge\Resources\Domain
     */
    public function updateDomain($organizationId, $serverId, $siteId, $domainId, array $data)
    {
        $domain = $this->patch(
            "orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/domains/{$domainId}",
            $data
        )['data'] ?? [];

        return new Domain($domain, $this);
    }

    /**
     * Delete the given domain.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $domainId
     * @return void
     */
    public function deleteDomain($organizationId, $serverId, $siteId, $domainId)
    {
        $this->delete("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/domains/{$domainId}");
    }

    /**
     * Get domain DNS configurations.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $domainId
     * @return array
     */
    public function domainConfigurations($organizationId, $serverId, $siteId, $domainId)
    {
        return $this->get("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/domains/{$domainId}/configurations")['data'] ?? [];
    }

    /**
     * Create a domain action.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $domainId
     * @param  array  $data
     * @return mixed
     */
    public function createDomainAction($organizationId, $serverId, $siteId, $domainId, array $data)
    {
        return $this->post(
            "orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/domains/{$domainId}/actions",
            $data
        );
    }

    /**
     * Get a domain certificate.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $domainId
     * @return \Laravel\Forge\Resources\Certificate
     */
    public function domainCertificate($organizationId, $serverId, $siteId, $domainId)
    {
        return new Certificate(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/domains/{$domainId}/certificate")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a domain certificate.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $domainId
     * @param  array  $data
     * @return \Laravel\Forge\Resources\Certificate
     */
    public function createDomainCertificate($organizationId, $serverId, $siteId, $domainId, array $data)
    {
        $certificate = $this->post(
            "orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/domains/{$domainId}/certificate",
            $data
        )['data'] ?? [];

        return new Certificate($certificate + ['organization_id' => $organizationId, 'server_id' => $serverId, 'site_id' => $siteId], $this);
    }

    /**
     * Delete the given domain certificate.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $domainId
     * @return void
     */
    public function deleteDomainCertificate($organizationId, $serverId, $siteId, $domainId)
    {
        $this->delete("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/domains/{$domainId}/certificate");
    }

    /**
     * Create a domain certificate action.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $domainId
     * @param  array  $data
     * @return mixed
     */
    public function createDomainCertificateAction($organizationId, $serverId, $siteId, $domainId, array $data)
    {
        return $this->post(
            "orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/domains/{$domainId}/certificate/actions",
            $data
        );
    }

    /**
     * Get the collection of workers for a site.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Worker[]
     */
    public function workers($organizationId, $serverId, $siteId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/workers")['data'] ?? [],
            Worker::class,
            ['organization_id' => $organizationId, 'server_id' => $serverId, 'site_id' => $siteId]
        );
    }

    /**
     * Create a new worker.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  array  $data
     * @return \Laravel\Forge\Resources\Worker
     */
    public function createWorker($organizationId, $serverId, $siteId, array $data)
    {
        $worker = $this->post(
            "orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/workers",
            $data
        )['data'] ?? [];

        return new Worker($worker + ['organization_id' => $organizationId, 'server_id' => $serverId, 'site_id' => $siteId], $this);
    }

    /**
     * Get a worker instance.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $workerId
     * @return \Laravel\Forge\Resources\Worker
     */
    public function worker($organizationId, $serverId, $siteId, $workerId)
    {
        return new Worker(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/workers/{$workerId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Delete the given worker.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $workerId
     * @return void
     */
    public function deleteWorker($organizationId, $serverId, $siteId, $workerId)
    {
        $this->delete("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/workers/{$workerId}");
    }

    /**
     * Create a worker action.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $workerId
     * @param  array  $data
     * @return mixed
     */
    public function createWorkerAction($organizationId, $serverId, $siteId, $workerId, array $data)
    {
        return $this->post(
            "orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/workers/{$workerId}/actions",
            $data
        );
    }

    /**
     * Get the site environment file.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return string
     */
    public function siteEnvironment($organizationId, $serverId, $siteId)
    {
        $response = $this->get("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/environment");

        return $response['data']['content'] ?? $response['content'] ?? '';
    }

    /**
     * Update the site environment file.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $content
     * @return void
     */
    public function updateSiteEnvironment($organizationId, $serverId, $siteId, $content)
    {
        $this->put("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/environment", [
            'content' => $content,
        ]);
    }

    /**
     * Get the site Nginx configuration.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return string
     */
    public function siteNginx($organizationId, $serverId, $siteId)
    {
        $response = $this->get("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/nginx");

        return $response['data']['content'] ?? $response['content'] ?? '';
    }

    /**
     * Update the site Nginx configuration.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $content
     * @return void
     */
    public function updateSiteNginx($organizationId, $serverId, $siteId, $content)
    {
        $this->put("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/nginx", [
            'content' => $content,
        ]);
    }

    /**
     * Get the site PHP version.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return array
     */
    public function sitePhp($organizationId, $serverId, $siteId)
    {
        return $this->get("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/php")['data'] ?? [];
    }

    /**
     * Update the site PHP version.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  array  $data
     * @return void
     */
    public function updateSitePhp($organizationId, $serverId, $siteId, array $data)
    {
        $this->put("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/php", $data);
    }

    /**
     * Get the Nginx access log.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return string
     */
    public function siteNginxAccessLog($organizationId, $serverId, $siteId)
    {
        $response = $this->get("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/logs/nginx-access");

        return $response['data']['content'] ?? $response['content'] ?? '';
    }

    /**
     * Delete the Nginx access log.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return void
     */
    public function deleteSiteNginxAccessLog($organizationId, $serverId, $siteId)
    {
        $this->delete("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/logs/nginx-access");
    }

    /**
     * Get the Nginx error log.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return string
     */
    public function siteNginxErrorLog($organizationId, $serverId, $siteId)
    {
        $response = $this->get("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/logs/nginx-error");

        return $response['data']['content'] ?? $response['content'] ?? '';
    }

    /**
     * Delete the Nginx error log.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return void
     */
    public function deleteSiteNginxErrorLog($organizationId, $serverId, $siteId)
    {
        $this->delete("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/logs/nginx-error");
    }

    /**
     * Get the application log.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return string
     */
    public function siteApplicationLog($organizationId, $serverId, $siteId)
    {
        $response = $this->get("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/logs/application");

        return $response['data']['content'] ?? $response['content'] ?? '';
    }

    /**
     * Delete the application log.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return void
     */
    public function deleteSiteApplicationLog($organizationId, $serverId, $siteId)
    {
        $this->delete("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/logs/application");
    }

    /**
     * Get the collection of heartbeats for a site.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Heartbeat[]
     */
    public function heartbeats($organizationId, $serverId, $siteId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/heartbeats")['data'] ?? [],
            Heartbeat::class,
            ['organization_id' => $organizationId, 'server_id' => $serverId, 'site_id' => $siteId]
        );
    }

    /**
     * Create a new heartbeat.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  array  $data
     * @return \Laravel\Forge\Resources\Heartbeat
     */
    public function createHeartbeat($organizationId, $serverId, $siteId, array $data)
    {
        $heartbeat = $this->post(
            "orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/heartbeats",
            $data
        )['data'] ?? [];

        return new Heartbeat($heartbeat + ['organization_id' => $organizationId, 'server_id' => $serverId, 'site_id' => $siteId], $this);
    }

    /**
     * Get a heartbeat instance.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $heartbeatId
     * @return \Laravel\Forge\Resources\Heartbeat
     */
    public function heartbeat($organizationId, $serverId, $siteId, $heartbeatId)
    {
        return new Heartbeat(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/heartbeats/{$heartbeatId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Update a heartbeat.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $heartbeatId
     * @param  array  $data
     * @return \Laravel\Forge\Resources\Heartbeat
     */
    public function updateHeartbeat($organizationId, $serverId, $siteId, $heartbeatId, array $data)
    {
        $heartbeat = $this->put(
            "orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/heartbeats/{$heartbeatId}",
            $data
        )['data'] ?? [];

        return new Heartbeat($heartbeat, $this);
    }

    /**
     * Delete the given heartbeat.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @param  string  $heartbeatId
     * @return void
     */
    public function deleteHeartbeat($organizationId, $serverId, $siteId, $heartbeatId)
    {
        $this->delete("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/heartbeats/{$heartbeatId}");
    }
}
