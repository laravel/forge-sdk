<?php

namespace Themsaid\Forge\Actions;

use Themsaid\Forge\Resources\Server;

trait ManagesServers
{
    /**
     * Get the collection of servers.
     *
     * @return Server[]
     */
    public function servers()
    {
        return $this->transformCollection(
            $this->get('servers')['servers'], Server::class
        );
    }

    /**
     * Get a server instance.
     *
     * @param  string $serverId
     * @return Server
     */
    public function server($serverId)
    {
        return new Server($this->get("servers/$serverId")['server'], $this);
    }

    /**
     * Create a new server.
     *
     * @param  array $data
     * @return Server
     */
    public function createServer(array $data)
    {
        return new Server($this->post('servers', $data)['server'], $this);
    }

    /**
     * Update the given server.
     *
     * @param  string $serverId
     * @param  array $data
     * @return Server
     */
    public function updateServer($serverId, array $data)
    {
        return $this->put("servers/$serverId", $data)['server'];
    }

    /**
     * Delete the given server.
     *
     * @param  string $serverId
     * @return void
     */
    public function deleteServer($serverId)
    {
        $this->delete("servers/$serverId");
    }

    /**
     * Revoke forge access to the server.
     *
     * @param  string $serverId
     * @return void
     */
    public function revokeAccessToServer($serverId)
    {
        $this->post("servers/$serverId/revoke");
    }

    /**
     * Reconnect the server to Forge with a new key.
     *
     * @param  string $serverId
     * @return string
     */
    public function reconnectToServer($serverId)
    {
        $this->post("servers/$serverId/reconnect")['public_key'];
    }

    /**
     * Reactivate a revoked server.
     *
     * @param  string $serverId
     * @return void
     */
    public function reactivateToServer($serverId)
    {
        $this->post("servers/$serverId/reactivate");
    }

    /**
     * Reboot the server.
     *
     * @param  string $serverId
     * @return void
     */
    public function rebootServer($serverId)
    {
        $this->post("servers/$serverId/reboot");
    }

    /**
     * Reboot MySQL on the server.
     *
     * @param  string $serverId
     * @return void
     */
    public function rebootMysql($serverId)
    {
        $this->post("servers/$serverId/mysql/reboot");
    }

    /**
     * Stop MySQL on the server.
     *
     * @param  string $serverId
     * @return void
     */
    public function stopMysql($serverId)
    {
        $this->post("servers/$serverId/mysql/stop");
    }

    /**
     * Reboot Postgres on the server.
     *
     * @param  string $serverId
     * @return void
     */
    public function rebootPostgres($serverId)
    {
        $this->post("servers/$serverId/postgres/reboot");
    }

    /**
     * Stop Postgres on the server.
     *
     * @param  string $serverId
     * @return void
     */
    public function stopPostgres($serverId)
    {
        $this->post("servers/$serverId/postgres/stop");
    }

    /**
     * Reboot Nginx on the server.
     *
     * @param  string $serverId
     * @return void
     */
    public function rebootNginx($serverId)
    {
        $this->post("servers/$serverId/nginx/reboot");
    }

    /**
     * Stop Nginx on the server.
     *
     * @param  string $serverId
     * @return void
     */
    public function stopNginx($serverId)
    {
        $this->post("servers/$serverId/nginx/stop");
    }

    /**
     * Install Blackfire on the server.
     *
     * @param  string $serverId
     * @param  array $data
     * @return Server
     */
    public function installBlackfire($serverId, array $data)
    {
        $this->post("servers/$serverId/blackfire/install", $data);
    }

    /**
     * Remove Blackfire from the server.
     *
     * @param  string $serverId
     * @return void
     */
    public function removeBlackfire($serverId)
    {
        $this->delete("servers/$serverId/blackfire/remove");
    }

    /**
     * Install Papertrail on the server.
     *
     * @param  string $serverId
     * @param  array $data
     * @return Server
     */
    public function installPapertrail($serverId, array $data)
    {
        $this->post("servers/$serverId/papertrail/install", $data);
    }

    /**
     * Remove Papertrail from the server.
     *
     * @param  string $serverId
     * @param  array $data
     * @return Server
     */
    public function removePapertrail($serverId)
    {
        $this->delete("servers/$serverId/papertrail/remove");
    }
}