<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Site;

trait ManagesSites
{
    /**
     * Get the collection of sites.
     *
     * @param  int  $serverId
     * @return \Laravel\Forge\Resources\Site[]
     */
    public function sites($serverId)
    {
        return $this->transformCollection(
            $this->get("servers/$serverId/sites")['sites'],
            Site::class,
            ['server_id' => $serverId]
        );
    }

    /**
     * Get a site instance.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @return \Laravel\Forge\Resources\Site
     */
    public function site($serverId, $siteId)
    {
        return new Site(
            $this->get("servers/$serverId/sites/$siteId")['site'] + ['server_id' => $serverId], $this
        );
    }

    /**
     * Create a new site.
     *
     * @param  int  $serverId
     * @param  array  $data
     * @param  bool  $wait
     * @return \Laravel\Forge\Resources\Site
     */
    public function createSite($serverId, array $data, $wait = true)
    {
        $site = $this->post("servers/$serverId/sites", $data)['site'];

        if ($wait) {
            return $this->retry($this->getTimeout(), function () use ($serverId, $site) {
                $site = $this->site($serverId, $site['id']);

                return $site->status == 'installed' ? $site : null;
            });
        }

        return new Site($site + ['server_id' => $serverId], $this);
    }

    /**
     * Update the given site.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @param  array  $data
     * @return \Laravel\Forge\Resources\Site
     */
    public function updateSite($serverId, $siteId, array $data)
    {
        return new Site(
            $this->request('PUT', "servers/$serverId/sites/$siteId", ['json' => $data])['site']
            + ['server_id' => $serverId], $this
        );
    }

    /**
     * Add Site Aliases.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @param  array  $aliases
     * @return \Laravel\Forge\Resources\Site
     */
    public function addSiteAliases($serverId, $siteId, array $aliases)
    {
        return new Site(
            $this->put("servers/$serverId/sites/$siteId/aliases", compact('aliases'))['site']
            + ['server_id' => $serverId], $this
        );
    }

    /**
     * Refresh the site token.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @return void
     */
    public function refreshSiteToken($serverId, $siteId)
    {
        $this->post("servers/$serverId/sites/$siteId/refresh");
    }

    /**
     * Delete the given site.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @return void
     */
    public function deleteSite($serverId, $siteId)
    {
        $this->delete("servers/$serverId/sites/$siteId");
    }

    /**
     * Get the content of the site's Nginx configuration file.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @return string
     */
    public function siteNginxFile($serverId, $siteId)
    {
        return $this->get("servers/$serverId/sites/$siteId/nginx");
    }

    /**
     * Update the content of the site's Nginx configuration file.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @param  string  $content
     * @return void
     */
    public function updateSiteNginxFile($serverId, $siteId, $content)
    {
        $this->put("servers/$serverId/sites/$siteId/nginx", compact('content'));
    }

    /**
     * Get the content of the site's Environment file.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @return string
     */
    public function siteEnvironmentFile($serverId, $siteId)
    {
        return $this->get("servers/$serverId/sites/$siteId/env");
    }

    /**
     * Update the content of the site's Environment file.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @param  string  $content
     * @return void
     */
    public function updateSiteEnvironmentFile($serverId, $siteId, $content)
    {
        $this->put("servers/$serverId/sites/$siteId/env", compact('content'));
    }

    /**
     * Install a git repository on the given site.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @param  array  $data
     * @param  bool  $wait
     * @return Site
     */
    public function installGitRepositoryOnSite($serverId, $siteId, array $data, $wait = true)
    {
        $site = $this->post("servers/$serverId/sites/$siteId/git", $data);

        if ($wait) {
            return $this->retry($this->getTimeout(), function () use ($serverId, $siteId) {
                $site = $this->site($serverId, $siteId);

                return $site->repositoryStatus === 'installed' ? $site : null;
            });
        }

        return new Site($site + ['server_id' => $serverId], $this);
    }

    /**
     * Update the site's git repository parameters.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @param  array  $data
     * @return void
     */
    public function updateSiteGitRepository($serverId, $siteId, array $data)
    {
        $this->put("servers/$serverId/sites/$siteId/git", $data);
    }

    /**
     * Destroy the git-based project installed on the site.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @param  bool  $wait
     * @return void
     */
    public function destroySiteGitRepository($serverId, $siteId, $wait = true)
    {
        $this->delete("servers/$serverId/sites/$siteId/git");

        if ($wait) {
            $this->retry($this->getTimeout(), function () use ($serverId, $siteId) {
                return is_null($this->site($serverId, $siteId)->repositoryStatus);
            });
        }
    }

    /**
     * Get the content of the site's deployment script.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @return string
     */
    public function siteDeploymentScript($serverId, $siteId)
    {
        return $this->get("servers/$serverId/sites/$siteId/deployment/script");
    }

    /**
     * Update the content of the site's deployment script.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @param  string  $content
     * @return void
     */
    public function updateSiteDeploymentScript($serverId, $siteId, $content)
    {
        $this->put("servers/$serverId/sites/$siteId/deployment/script", compact('content'));
    }

    /**
     * Enable "Quick Deploy" for the given site.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @return void
     */
    public function enableQuickDeploy($serverId, $siteId)
    {
        $this->post("servers/$serverId/sites/$siteId/deployment");
    }

    /**
     * Disable "Quick Deploy" for the given site.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @return void
     */
    public function disableQuickDeploy($serverId, $siteId)
    {
        $this->delete("servers/$serverId/sites/$siteId/deployment");
    }

    /**
     * Deploy the given site.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @param  bool  $wait
     * @return \Laravel\Forge\Resources\Site
     */
    public function deploySite($serverId, $siteId, $wait = true)
    {
        $site = $this->post("servers/$serverId/sites/$siteId/deployment/deploy");

        if ($wait) {
            return $this->retry($this->getTimeout(), function () use ($serverId, $siteId) {
                $site = $this->site($serverId, $siteId);

                return is_null($site->deploymentStatus) ? $site : null;
            });
        }

        return new Site($site + ['server_id' => $serverId], $this);
    }

    /**
     * Reset the deployment state of the given site.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @return void
     */
    public function resetDeploymentState($serverId, $siteId)
    {
        $this->post("servers/$serverId/sites/$siteId/deployment/reset");
    }

    /**
     * Get the last deployment log of the site.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @return string
     */
    public function siteDeploymentLog($serverId, $siteId)
    {
        return $this->get("servers/$serverId/sites/$siteId/deployment/log");
    }

    /**
     * Get the deployment history of the site.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @return string
     */
    public function deploymentHistory($serverId, $siteId)
    {
        return $this->get("/api/v1/servers/$serverId/sites/$siteId/deployment-history");
    }

    /**
     * Get a single deployment from the deployment history of a site.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @param  int  $deploymentId
     * @return string
     */
    public function deploymentHistoryDeployment($serverId, $siteId, $deploymentId)
    {
        return $this->get("/api/v1/servers/$serverId/sites/$siteId/deployment-history/$deploymentId");
    }

    /**
     * Get the output for a deployment of the site.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @param  int  $deploymentId
     * @return string
     */
    public function deploymentHistoryOutput($serverId, $siteId, $deploymentId)
    {
        return $this->get("/api/v1/servers/$serverId/sites/$siteId/deployment-history/$deploymentId/output");
    }

    /**
     * Enable Hipchat Notifications for the given site.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @param  array  $data
     * @return void
     */
    public function enableHipchatNotifications($serverId, $siteId, array $data)
    {
        $this->post("servers/$serverId/sites/$siteId/notify/hipchat", $data);
    }

    /**
     * Disable Hipchat Notifications for the given site.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @return void
     */
    public function disableHipchatNotifications($serverId, $siteId)
    {
        $this->delete("servers/$serverId/sites/$siteId/notify/hipchat");
    }

    /**
     * Install a new WordPress project.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @param  array  $data
     * @return void
     */
    public function installWordPress($serverId, $siteId, array $data)
    {
        $this->post("servers/$serverId/sites/$siteId/wordpress", $data);
    }

    /**
     * Remove the WordPress project installed on the site.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @return void
     */
    public function removeWordPress($serverId, $siteId)
    {
        $this->delete("servers/$serverId/sites/$siteId/wordpress");
    }

    /**
     * Install a new phpMyAdmin project.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @param  array  $data
     * @return void
     */
    public function installPhpMyAdmin($serverId, $siteId, array $data)
    {
        $this->post("servers/$serverId/sites/$siteId/phpmyadmin", $data);
    }

    /**
     * Remove phpMyAdmin and revert the site back to a default state.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @return void
     */
    public function removePhpMyAdmin($serverId, $siteId)
    {
        $this->delete("servers/$serverId/sites/$siteId/phpmyadmin");
    }

    /**
     * Change the given site's PHP version.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @param  string  $version
     * @return void
     */
    public function changeSitePHPVersion($serverId, $siteId, $version)
    {
        $this->put("servers/$serverId/sites/$siteId/php", ['version' => $version]);
    }

    /**
     * Update the given site's balanced nodes.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @param  array  $data
     * @return void
     */
    public function updateNodeBalancingConfiguration($serverId, $siteId, array $data)
    {
        $this->put("servers/$serverId/sites/$siteId/balancing", $data);
    }

    /**
     * Get the given site's log.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @return string
     */
    public function siteLog($serverId, $siteId)
    {
        return $this->get("servers/$serverId/sites/$siteId/logs");
    }
}
