<?php

namespace Themsaid\Forge\Actions;

use Themsaid\Forge\Resources\Site;

trait ManagesSites
{
    /**
     * Get the collection of sites.
     *
     * @param  integer $serverId
     * @return Site[]
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
     * @param  integer $serverId
     * @param  integer $siteId
     * @return Site
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
     * @param  integer $serverId
     * @param  array $data
     * @param  boolean $wait
     * @return Site
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
     * @param  integer $serverId
     * @param  integer $siteId
     * @param  array $data
     * @return Site
     */
    public function updateSite($serverId, $siteId, array $data)
    {
        return new Site(
            $this->put("servers/$serverId/sites/$siteId", $data)['site']
            + ['server_id' => $serverId], $this
        );
    }

    /**
     * Refresh the site token.
     *
     * @param  integer $serverId
     * @param  integer $siteId
     * @return void
     */
    public function refreshSiteToken($serverId, $siteId)
    {
        $this->post("servers/$serverId/sites/$siteId/refresh");
    }

    /**
     * Delete the given site.
     *
     * @param  integer $serverId
     * @param  integer $siteId
     * @return void
     */
    public function deleteSite($serverId, $siteId)
    {
        $this->delete("servers/$serverId/sites/$siteId");
    }

    /**
     * Get the content of the site's Nginx configuration file.
     *
     * @param  integer $serverId
     * @param  integer $siteId
     * @return string
     */
    public function siteNginxFile($serverId, $siteId)
    {
        return $this->get("servers/$serverId/sites/$siteId/nginx");
    }

    /**
     * Update the content of the site's Nginx configuration file.
     *
     * @param  integer $serverId
     * @param  integer $siteId
     * @param  string $content
     * @return void
     */
    public function updateSiteNginxFile($serverId, $siteId, $content)
    {
        $this->put("servers/$serverId/sites/$siteId/nginx", compact('content'));
    }

    /**
     * Get the content of the site's Environment file.
     *
     * @param  integer $serverId
     * @param  integer $siteId
     * @return string
     */
    public function siteEnvironmentFile($serverId, $siteId)
    {
        return $this->get("servers/$serverId/sites/$siteId/env");
    }

    /**
     * Update the content of the site's Environment file.
     *
     * @param  integer $serverId
     * @param  integer $siteId
     * @param  string $content
     * @return void
     */
    public function updateSiteEnvironmentFile($serverId, $siteId, $content)
    {
        $this->put("servers/$serverId/sites/$siteId/env", compact('content'));
    }

    /**
     * Install a git repository on the given site.
     *
     * @param  integer $serverId
     * @param  integer $siteId
     * @param  array $data
     * @param  boolean $wait
     * @return void
     */
    public function installGitRepositoryOnSite($serverId, $siteId, array $data, $wait = false)
    {
        $this->post("servers/$serverId/sites/$siteId/git", $data);

        if ($wait) {
            $this->retry($this->getTimeout(), function () use ($serverId, $siteId) {
                $site = $this->site($serverId, $siteId);
                return $site->repositoryStatus === 'installed';
            });
        }
    }

    /**
     * Update the site's git repository parameters.
     *
     * @param  integer $serverId
     * @param  integer $siteId
     * @param  array $data
     * @return void
     */
    public function updateSiteGitRepository($serverId, $siteId, array $data)
    {
        $this->put("servers/$serverId/sites/$siteId/git", $data);
    }

    /**
     * Destroy the git-based project installed on the site.
     *
     * @param  integer $serverId
     * @param  integer $siteId
     * @param  boolean $wait
     * @return void
     */
    public function destroySiteGitRepository($serverId, $siteId, $wait = false)
    {
        $this->delete("servers/$serverId/sites/$siteId/git");

        if ($wait) {
            $this->retry($this->getTimeout(), function () use ($serverId, $siteId) {
                $site = $this->site($serverId, $siteId);
                return is_null($site->repositoryStatus);
            });
        }
    }

    /**
     * Get the content of the site's deployment script.
     *
     * @param  integer $serverId
     * @param  integer $siteId
     * @return string
     */
    public function siteDeploymentScript($serverId, $siteId)
    {
        return $this->get("servers/$serverId/sites/$siteId/deployment/script");
    }

    /**
     * Update the content of the site's deployment script.
     *
     * @param  integer $serverId
     * @param  integer $siteId
     * @param  string $content
     * @return void
     */
    public function updateSiteDeploymentScript($serverId, $siteId, $content)
    {
        $this->put("servers/$serverId/sites/$siteId/deployment/script", compact('content'));
    }

    /**
     * Enable "Quick Deploy" for the given site.
     *
     * @param  integer $serverId
     * @param  integer $siteId
     * @return void
     */
    public function enableQuickDeploy($serverId, $siteId)
    {
        $this->post("servers/$serverId/sites/$siteId/deployment");
    }

    /**
     * Disable "Quick Deploy" for the given site.
     *
     * @param  integer $serverId
     * @param  integer $siteId
     * @return void
     */
    public function disableQuickDeploy($serverId, $siteId)
    {
        $this->delete("servers/$serverId/sites/$siteId/deployment");
    }

    /**
     * Deploy the given site.
     *
     * @param  integer $serverId
     * @param  integer $siteId
     * @return void
     */
    public function deploySite($serverId, $siteId)
    {
        $this->post("servers/$serverId/sites/$siteId/deployment/deploy");
    }

    /**
     * Reset the deployment state of the given site.
     *
     * @param  integer $serverId
     * @param  integer $siteId
     * @return void
     */
    public function resetDeploymentState($serverId, $siteId)
    {
        $this->post("servers/$serverId/sites/$siteId/deployment/reset");
    }

    /**
     * Get the last deployment log of the site.
     *
     * @param  integer $serverId
     * @param  integer $siteId
     * @return string
     */
    public function siteDeploymentLog($serverId, $siteId)
    {
        return $this->get("servers/$serverId/sites/$siteId/deployment/log");
    }

    /**
     * Enable Hipchat Notifications for the given site.
     *
     * @param  integer $serverId
     * @param  integer $siteId
     * @param  array $data
     * @return void
     */
    public function enableHipchatNotifications($serverId, $siteId, array $data)
    {
        $this->post("servers/$serverId/sites/$siteId/notify/hipchat", $data);
    }

    /**
     * Disable Hipchat Notifications for the given site.
     *
     * @param  integer $serverId
     * @param  integer $siteId
     * @return void
     */
    public function disableHipchatNotifications($serverId, $siteId)
    {
        $this->delete("servers/$serverId/sites/$siteId/notify/hipchat");
    }

    /**
     * Install a new WordPress project.
     *
     * @param  integer $serverId
     * @param  integer $siteId
     * @param  array $data
     * @return void
     */
    public function installWordPress($serverId, $siteId, array $data)
    {
        $this->post("servers/$serverId/sites/$siteId/wordpress", $data);
    }

    /**
     * Remove the WordPress project installed on the site.
     *
     * @param  integer $serverId
     * @param  integer $siteId
     * @return void
     */
    public function removeWordPress($serverId, $siteId)
    {
        $this->delete("servers/$serverId/sites/$siteId/wordpress");
    }

    /**
     * Update the given site's balanced nodes.
     *
     * @param  integer $serverId
     * @param  integer $siteId
     * @param  array $data
     * @return void
     */
    public function updateNodeBalancingConfiguration($serverId, $siteId, array $data)
    {
        $this->put("servers/$serverId/sites/$siteId/balancing", $data);
    }
}