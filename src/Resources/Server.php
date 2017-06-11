<?php

namespace Themsaid\Forge\Resources;

class Server extends Resource
{
    /**
     * The id of the server.
     *
     * @var integer
     */
    public $id;

    /**
     * The name of the server.
     *
     * @var string
     */
    public $name;

    /**
     * The id of the provider credential instance.
     *
     * @var integer
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
     * Update the given server.
     *
     * @param  array $data
     * @return Server
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
        return $this->forge->deleteServer($this->id);
    }

    /**
     * Reboot the server.
     *
     * @return void
     */
    public function reboot()
    {
        return $this->forge->rebootServer($this->id);
    }

    /**
     * Revoke forge access to the server.
     *
     * @return void
     */
    public function revokeAccess()
    {
        return $this->forge->revokeAccessToServer($this->id);
    }

    /**
     * Reconnect the server to Forge with a new key.
     *
     * @return string
     */
    public function reconnect()
    {
        return $this->forge->reconnectToServer($this->id);
    }

    /**
     * Reactivate a revoked server.
     *
     * @return void
     */
    public function reactivate()
    {
        return $this->forge->reactivateToServer($this->id);
    }

    /**
     * Reboot MySQL on the server.
     *
     * @return void
     */
    public function rebootMysql()
    {
        return $this->forge->rebootMysql($this->id);
    }

    /**
     * Stop MySQL on the server.
     *
     * @return void
     */
    public function stopMysql()
    {
        return $this->forge->stopMysql($this->id);
    }

    /**
     * Reboot Postgres on the server.
     *
     * @return void
     */
    public function rebootPostgres()
    {
        return $this->forge->rebootPostgres($this->id);
    }

    /**
     * Stop Postgres on the server.
     *
     * @return void
     */
    public function stopPostgres()
    {
        return $this->forge->stopPostgres($this->id);
    }

    /**
     * Reboot Nginx on the server.
     *
     * @return void
     */
    public function rebootNginx()
    {
        return $this->forge->rebootNginx($this->id);
    }

    /**
     * Stop Nginx on the server.
     *
     * @return void
     */
    public function stopNginx()
    {
        return $this->forge->stopNginx($this->id);
    }

    /**
     * Install Blackfire on the server.
     *
     * @param  array $data
     * @return Server
     */
    public function installBlackfire(array $data)
    {
        return $this->forge->installBlackfire($this->id, $data);
    }

    /**
     * Remove Blackfire from the server.
     *
     * @return Server
     */
    public function removeBlackfire()
    {
        return $this->forge->removeBlackfire($this->id);
    }

    /**
     * Install Papertrail on the server.
     *
     * @param  array $data
     * @return Server
     */
    public function installPapertrail(array $data)
    {
        return $this->forge->installPapertrail($this->id, $data);
    }

    /**
     * Remove Papertrail from the server.
     *
     * @return Server
     */
    public function removePapertrail()
    {
        return $this->forge->removePapertrail($this->id);
    }

    /**
     * Upgrade the PHP version on the server.
     *
     * @return Server
     */
    public function upgradePHP()
    {
        return $this->forge->upgradePHP($this->id);
    }
}
