<?php

namespace Laravel\Forge\Resources;

class Server extends Resource
{
    /**
     * The id of the server.
     *
     * @var int
     */
    public $id;

    /**
     * The name of the server.
     *
     * @var string
     */
    public $name;

    /**
     * The type of the server.
     *
     * @var string
     */
    public $type;

    /**
     * The id of the provider credential instance.
     *
     * @var int
     */
    public $credentialId;

    /**
     * The size of the server.
     *
     * @var string
     */
    public $size;

    /**
     * The region of the server.
     *
     * @var string
     */
    public $region;

    /**
     * The IP address of the server.
     *
     * @var string
     */
    public $ipAddress;

    /**
     * The Private IP address of the server.
     *
     * @var string
     */
    public $privateIpAddress;

    /**
     * The PHP version used in the server.
     *
     * @var string
     */
    public $phpVersion;

    /**
     * The status of the Blackfire service.
     *
     * @var string
     */
    public $blackfireStatus;

    /**
     * The status of the Papertrail service.
     *
     * @var string
     */
    public $papertrailStatus;

    /**
     * Determine if the server installation is done.
     *
     * @var bool
     */
    public $isReady;

    /**
     * Determine if Forge access to the server was revoked.
     *
     * @var bool
     */
    public $revoked;

    /**
     * The date/time the server was created.
     *
     * @var string
     */
    public $createdAt;

    /**
     * The IDs of other servers on the same servers network.
     *
     * @var array
     */
    public $network = [];

    /**
     * The sudo password of the new server.
     *
     * @var string
     */
    public $sudoPassword;

    /**
     * The database password of the new server.
     *
     * @var string
     */
    public $databasePassword;

    /**
     * The provision command of the new server.
     *
     * @var string
     */
    public $provisionCommand;

    /**
     * Update the given server.
     *
     * @param  array  $data
     * @return \Laravel\Forge\Resources\Server
     */
    public function update(array $data)
    {
        return $this->forge->updateServer($this->id, $data);
    }

    /**
     * Delete the given server.
     *
     * @return void
     */
    public function delete()
    {
        $this->forge->deleteServer($this->id);
    }

    /**
     * Reboot the server.
     *
     * @return void
     */
    public function reboot()
    {
        $this->forge->rebootServer($this->id);
    }

    /**
     * Revoke forge access to the server.
     *
     * @return void
     */
    public function revokeAccess()
    {
        $this->forge->revokeAccessToServer($this->id);
    }

    /**
     * Reconnect the server to Forge with a new key.
     *
     * @return void
     */
    public function reconnect()
    {
        $this->forge->reconnectToServer($this->id);
    }

    /**
     * Reactivate a revoked server.
     *
     * @return void
     */
    public function reactivate()
    {
        $this->forge->reactivateToServer($this->id);
    }

    /**
     * Reboot MySQL on the server.
     *
     * @return void
     */
    public function rebootMysql()
    {
        $this->forge->rebootMysql($this->id);
    }

    /**
     * Stop MySQL on the server.
     *
     * @return void
     */
    public function stopMysql()
    {
        $this->forge->stopMysql($this->id);
    }

    /**
     * Reboot Postgres on the server.
     *
     * @return void
     */
    public function rebootPostgres()
    {
        $this->forge->rebootPostgres($this->id);
    }

    /**
     * Stop Postgres on the server.
     *
     * @return void
     */
    public function stopPostgres()
    {
        $this->forge->stopPostgres($this->id);
    }

    /**
     * Reboot Nginx on the server.
     *
     * @return void
     */
    public function rebootNginx()
    {
        $this->forge->rebootNginx($this->id);
    }

    /**
     * Stop Nginx on the server.
     *
     * @return void
     */
    public function stopNginx()
    {
        $this->forge->stopNginx($this->id);
    }

    /**
     * Reboot PHP on the server.
     *
     * @param  array  $data
     * @return void
     */
    public function rebootPHP(array $data)
    {
        $this->forge->rebootPHP($this->id, $data);
    }

    /**
     * Install Blackfire on the server.
     *
     * @param  array  $data
     * @return void
     */
    public function installBlackfire(array $data)
    {
        $this->forge->installBlackfire($this->id, $data);
    }

    /**
     * Remove Blackfire from the server.
     *
     * @return void
     */
    public function removeBlackfire()
    {
        $this->forge->removeBlackfire($this->id);
    }

    /**
     * Install Papertrail on the server.
     *
     * @param  array  $data
     * @return void
     */
    public function installPapertrail(array $data)
    {
        $this->forge->installPapertrail($this->id, $data);
    }

    /**
     * Remove Papertrail from the server.
     *
     * @return void
     */
    public function removePapertrail()
    {
        $this->forge->removePapertrail($this->id);
    }

    /**
     * Enable OPCache on the server.
     *
     * @return void
     */
    public function enableOPCache()
    {
        $this->forge->enableOPCache($this->id);
    }

    /**
     * Disable OPCache on the server.
     *
     * @return void
     */
    public function disableOPCache()
    {
        $this->forge->disableOPCache($this->id);
    }

    /**
     * Get the collection of PHP Versions.
     *
     * @return \Laravel\Forge\Resources\PHPVersion[]
     */
    public function phpVersions()
    {
        return $this->forge->phpVersions($this->id);
    }

    /**
     * Install a version of PHP.
     *
     * @param  string  $version
     * @return void
     */
    public function installPHP($version)
    {
        $this->forge->installPHP($this->id, $version);
    }

    /**
     * Patch the version of PHP.
     *
     * @param  string  $version
     * @return void
     */
    public function updatePHP($version)
    {
        $this->forge->updatePHP($this->id, $version);
    }
}
