<?php

namespace Themsaid\Forge\Resources;

class Site extends Resource
{
    /**
     * The id of the site.
     *
     * @var integer
     */
    public $id;

    /**
     * The id of the server.
     *
     * @var integer
     */
    public $serverId;

    /**
     * The name of the site.
     *
     * @var string
     */
    public $name;

    /**
     * The name of the directory housing the site.
     *
     * @var string
     */
    public $directory;

    /**
     * Determine if the site allows Wildcard Sub-Domains.
     *
     * @var boolean
     */
    public $wildcards;

    /**
     * The status of the site.
     *
     * @var string
     */
    public $status;

    /**
     * The deployment git repository.
     *
     * @var string
     */
    public $repository;

    /**
     * The type of the repository provider.
     *
     * @var string
     */
    public $repositoryProvider;

    /**
     * The name of the deployment branch.
     *
     * @var string
     */
    public $repositoryBranch;

    /**
     * The status of the repository.
     *
     * @var string
     */
    public $repositoryStatus;

    /**
     * Determine if "Quick Deploy" is enabled for the site.
     *
     * @var boolean
     */
    public $quickDeploy;

    /**
     * The status of the deployment.
     *
     * @var string|null
     */
    public $deploymentStatus;

    /**
     * The type of the project installed on the site.
     *
     * @var string
     */
    public $projectType;

    /**
     * The application installed on the site.
     *
     * @var string
     */
    public $app;

    /**
     * The status of the application installed.
     *
     * @var string
     */
    public $appStatus;

    /**
     * Hipchat room for deployment notifications.
     *
     * @var string
     */
    public $hipchatRoom;

    /**
     * Slack channel for deployment notifications.
     *
     * @var string
     */
    public $slackChannel;

    /**
     * The status of load balancing.
     *
     * @var string
     */
    public $balancingStatus;

    /**
     * The The date/time the site was created.
     *
     * @var string
     */
    public $createdAt;

    /**
     * Refresh the site token.
     *
     * @return void
     */
    public function refreshToken()
    {
        return $this->forge->refreshSiteToken($this->serverId, $this->id);
    }

    /**
     * Delete the given site.
     *
     * @return void
     */
    public function delete()
    {
        $this->forge->deleteSite($this->serverId, $this->id);
    }

    /**
     * Install a git repository on the given site.
     *
     * @param  array $data
     * @param  boolean $wait
     * @return void
     */
    public function installGitRepository(array $data, $wait = false)
    {
        return $this->forge->installGitRepositoryOnSite($this->serverId, $this->id, $data, $wait);
    }

    /**
     * Update the site's git repository parameters.
     *
     * @param  array $data
     * @return void
     */
    public function updateGitRepository(array $data)
    {
        return $this->forge->updateSiteGitRepository($this->serverId, $this->id, $data);
    }

    /**
     * Destroy the git-based project installed on the site.
     *
     * @param boolean $wait
     * @return void
     */
    public function destroyGitRepository($wait = false)
    {
        return $this->forge->destroySiteGitRepository($this->serverId, $this->id, $wait);
    }

    /**
     * Get the content of the site's deployment script.
     *
     * @return string
     */
    public function getDeploymentScript()
    {
        return $this->forge->siteDeploymentScript($this->serverId, $this->id);
    }

    /**
     * Update the content of the site's deployment script.
     *
     * @param  string $content
     * @return void
     */
    public function updateDeploymentScript($content)
    {
        return $this->forge->updateSiteDeploymentScript($this->serverId, $this->id, $content);
    }

    /**
     * Enable "Quick Deploy" for the given site.
     *
     * @return void
     */
    public function enableQuickDeploy()
    {
        return $this->forge->enableQuickDeploy($this->serverId, $this->id);
    }

    /**
     * Disable "Quick Deploy" for the given site.
     *
     * @return void
     */
    public function disableQuickDeploy()
    {
        return $this->forge->disableQuickDeploy($this->serverId, $this->id);
    }

    /**
     * Deploy the given site.
     *
     * @return void
     */
    public function deploySite()
    {
        return $this->forge->deploySite($this->serverId, $this->id);
    }

    /**
     * Enable Hipchat Notifications for the given site.
     *
     * @param  array $data
     * @return void
     */
    public function enableHipchatNotifications(array $data)
    {
        return $this->forge->enableHipchatNotifications($this->serverId, $this->id, $data);
    }

    /**
     * Disable Hipchat Notifications for the given site.
     *
     * @return void
     */
    public function disableHipchatNotifications()
    {
        return $this->forge->disableHipchatNotifications($this->serverId, $this->id);
    }

    /**
     * Install a new WordPress project.
     *
     * @return void
     */
    public function installWordPress($data)
    {
        return $this->forge->installWordPress($this->serverId, $this->id, $data);
    }

    /**
     * Remove the WordPress project installed on the site.
     *
     * @return void
     */
    public function removeWordPress()
    {
        return $this->forge->removeWordPress($this->serverId, $this->id);
    }
}

