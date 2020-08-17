<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Event;
use Laravel\Forge\Resources\Server;

trait ManagesServers
{
    /**
     * Get the collection of servers.
     *
     * @return \Laravel\Forge\Resources\Server[]
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
     * @param  string  $serverId
     * @return \Laravel\Forge\Resources\Server
     */
    public function server($serverId)
    {
        return new Server($this->get("servers/$serverId")['server'], $this);
    }

    /**
     * Create a new server. API recommends a 2 minute delay between checks.
     *
     * @param  array  $data
     * @param  bool  $wait
     * @param  int  $timeout
     * @return \Laravel\Forge\Resources\Server
     */
    public function createServer(array $data, $wait = false, $timeout = 900)
    {
        $response = $this->post('servers', $data);

        $server = $response['server'];
        $server['sudo_password'] = @$response['sudo_password'];
        $server['database_password'] = @$response['database_password'];
        $server['provision_command'] = @$response['provision_command'];

        if ($wait) {
            return $this->retry($timeout, function () use ($server) {
                $server = $this->server($server['id']);
                return $server->isReady ? $server : null;
            }, 120);
        }

        return new Server($server, $this);
    }

    /**
     * Update the given server.
     *
     * @param  string  $serverId
     * @param  array  $data
     * @return \Laravel\Forge\Resources\Server
     */
    public function updateServer($serverId, array $data)
    {
        return $this->put("servers/$serverId", $data)['server'];
    }

    /**
     * Delete the given server.
     *
     * @param  string  $serverId
     * @return void
     */
    public function deleteServer($serverId)
    {
        $this->delete("servers/$serverId");
    }

    /**
     * Revoke forge access to the server.
     *
     * @param  string  $serverId
     * @return void
     */
    public function revokeAccessToServer($serverId)
    {
        $this->post("servers/$serverId/revoke");
    }

    /**
     * Reconnect the server to Forge with a new key.
     *
     * @param  string  $serverId
     * @return void
     */
    public function reconnectToServer($serverId)
    {
        $this->post("servers/$serverId/reconnect")['public_key'];
    }

    /**
     * Reactivate a revoked server.
     *
     * @param  string  $serverId
     * @return void
     */
    public function reactivateToServer($serverId)
    {
        $this->post("servers/$serverId/reactivate");
    }

    /**
     * Reboot the server.
     *
     * @param  string  $serverId
     * @return void
     */
    public function rebootServer($serverId)
    {
        $this->post("servers/$serverId/reboot");
    }

    /**
     * Reboot MySQL on the server.
     *
     * @param  string  $serverId
     * @return void
     */
    public function rebootMysql($serverId)
    {
        $this->post("servers/$serverId/mysql/reboot");
    }

    /**
     * Stop MySQL on the server.
     *
     * @param  string  $serverId
     * @return void
     */
    public function stopMysql($serverId)
    {
        $this->post("servers/$serverId/mysql/stop");
    }

    /**
     * Reboot Postgres on the server.
     *
     * @param  string  $serverId
     * @return void
     */
    public function rebootPostgres($serverId)
    {
        $this->post("servers/$serverId/postgres/reboot");
    }

    /**
     * Stop Postgres on the server.
     *
     * @param  string  $serverId
     * @return void
     */
    public function stopPostgres($serverId)
    {
        $this->post("servers/$serverId/postgres/stop");
    }

    /**
     * Reboot Nginx on the server.
     *
     * @param  string  $serverId
     * @return void
     */
    public function rebootNginx($serverId)
    {
        $this->post("servers/$serverId/nginx/reboot");
    }

    /**
     * Stop Nginx on the server.
     *
     * @param  string  $serverId
     * @return void
     */
    public function stopNginx($serverId)
    {
        $this->post("servers/$serverId/nginx/stop");
    }

    /**
     * Reboot PHP on the server.
     *
     * @param  string  $serverId
     * @param  array  $data
     * @return void
     */
    public function rebootPHP($serverId, $data)
    {
        $this->post("servers/$serverId/php/reboot", $data);
    }

    /**
     * Install Blackfire on the server.
     *
     * @param  string  $serverId
     * @param  array  $data
     * @return void
     */
    public function installBlackfire($serverId, array $data)
    {
        $this->post("servers/$serverId/blackfire/install", $data);
    }

    /**
     * Remove Blackfire from the server.
     *
     * @param  string  $serverId
     * @return void
     */
    public function removeBlackfire($serverId)
    {
        $this->delete("servers/$serverId/blackfire/remove");
    }

    /**
     * Install Papertrail on the server.
     *
     * @param  string  $serverId
     * @param  array  $data
     * @return void
     */
    public function installPapertrail($serverId, array $data)
    {
        $this->post("servers/$serverId/papertrail/install", $data);
    }

    /**
     * Remove Papertrail from the server.
     *
     * @param  string  $serverId
     * @return void
     */
    public function removePapertrail($serverId)
    {
        $this->delete("servers/$serverId/papertrail/remove");
    }

    /**
     * Enable OPCache on the server.
     *
     * @param  string  $serverId
     * @return void
     */
    public function enableOPCache($serverId)
    {
        $this->post("servers/$serverId/php/opcache");
    }

    /**
     * Disable OPCache on the server.
     *
     * @param  string  $serverId
     * @return void
     */
    public function disableOPCache($serverId)
    {
        $this->delete("servers/$serverId/php/opcache");
    }

    /**
     * Upgrade to latest PHP version.
     *
     * @param  string  $serverId
     * @return void
     */
    public function upgradePHP($serverId)
    {
        $this->post("servers/$serverId/php/upgrade");
    }

    /**
     * Get recent events
     *
     * @param  string|null  $serverId
     * @return \Laravel\Forge\Resources\Event[]
     */
    public function events($serverId = null)
    {
        $endpoint = is_null($serverId) ? "servers/events" : "servers/events?server_id=" . $serverId;

        return $this->transformCollection(
            $this->get($endpoint),
            Event::class
        );
    }
}
