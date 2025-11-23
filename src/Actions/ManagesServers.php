<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Server;
use Laravel\Forge\Resources\Event;
use Laravel\Forge\Resources\PHPVersion;

trait ManagesServers
{
    /**
     * Get the collection of servers for an organization.
     *
     * @param  string  $organizationId
     * @return \Laravel\Forge\Resources\Server[]
     */
    public function servers($organizationId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationId}/servers")['data'] ?? [],
            Server::class,
            ['organization_id' => $organizationId]
        );
    }

    /**
     * Get a server instance.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @return \Laravel\Forge\Resources\Server
     */
    public function server($organizationId, $serverId)
    {
        return new Server(
            $this->get("orgs/{$organizationId}/servers/{$serverId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a new server.
     *
     * @param  string  $organizationId
     * @param  array  $data
     * @param  bool  $wait
     * @return \Laravel\Forge\Resources\Server
     */
    public function createServer($organizationId, array $data, $wait = true)
    {
        $server = $this->post("orgs/{$organizationId}/servers", $data)['data'] ?? [];

        if ($wait) {
            return $this->retry($this->getTimeout(), function () use ($organizationId, $server) {
                $srv = $this->server($organizationId, $server['id']);
                return isset($srv->isReady) && $srv->isReady ? $srv : null;
            });
        }

        return new Server($server + ['organization_id' => $organizationId], $this);
    }

    /**
     * Delete a server.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @return void
     */
    public function deleteServer($organizationId, $serverId)
    {
        $this->delete("orgs/{$organizationId}/servers/{$serverId}");
    }

    /**
     * Get the collection of archived servers for an organization.
     *
     * @param  string  $organizationId
     * @return \Laravel\Forge\Resources\Server[]
     */
    public function archivedServers($organizationId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationId}/servers/archives")['data'] ?? [],
            Server::class,
            ['organization_id' => $organizationId]
        );
    }

    /**
     * Create an archived server.
     *
     * @param  string  $organizationId
     * @param  array  $data
     * @return \Laravel\Forge\Resources\Server
     */
    public function createArchivedServer($organizationId, array $data)
    {
        $server = $this->post("orgs/{$organizationId}/servers/archives", $data)['data'] ?? [];

        return new Server($server + ['organization_id' => $organizationId], $this);
    }

    /**
     * Delete an archived server.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @return void
     */
    public function deleteArchivedServer($organizationId, $serverId)
    {
        $this->delete("orgs/{$organizationId}/servers/archives/{$serverId}");
    }

    /**
     * Create a server action.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  array  $data
     * @return array
     */
    public function createServerAction($organizationId, $serverId, array $data)
    {
        return $this->post("orgs/{$organizationId}/servers/{$serverId}/actions", $data);
    }

    /**
     * Perform an action on a background process.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $backgroundProcessId
     * @param  array  $data
     * @return array
     */
    public function performBackgroundProcessAction($organizationId, $serverId, $backgroundProcessId, array $data)
    {
        return $this->post("orgs/{$organizationId}/servers/{$serverId}/background-processes/{$backgroundProcessId}/actions", $data);
    }

    /**
     * Perform a Nginx service action.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  array  $data
     * @return array
     */
    public function performNginxAction($organizationId, $serverId, array $data)
    {
        return $this->post("orgs/{$organizationId}/servers/{$serverId}/services/nginx/actions", $data);
    }

    /**
     * Perform a Postgres service action.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  array  $data
     * @return array
     */
    public function performPostgresAction($organizationId, $serverId, array $data)
    {
        return $this->post("orgs/{$organizationId}/servers/{$serverId}/services/postgres/actions", $data);
    }

    /**
     * Perform a Redis service action.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  array  $data
     * @return array
     */
    public function performRedisAction($organizationId, $serverId, array $data)
    {
        return $this->post("orgs/{$organizationId}/servers/{$serverId}/services/redis/actions", $data);
    }

    /**
     * Perform a MySQL service action.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  array  $data
     * @return array
     */
    public function performMySQLAction($organizationId, $serverId, array $data)
    {
        return $this->post("orgs/{$organizationId}/servers/{$serverId}/services/mysql/actions", $data);
    }

    /**
     * Perform a PHP service action.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  array  $data
     * @return array
     */
    public function performPHPAction($organizationId, $serverId, array $data)
    {
        return $this->post("orgs/{$organizationId}/servers/{$serverId}/services/php/actions", $data);
    }

    /**
     * Perform a Supervisor service action.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  array  $data
     * @return array
     */
    public function performSupervisorAction($organizationId, $serverId, array $data)
    {
        return $this->post("orgs/{$organizationId}/servers/{$serverId}/services/supervisor/actions", $data);
    }

    /**
     * Get the collection of server events.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @return \Laravel\Forge\Resources\Event[]
     */
    public function serverEvents($organizationId, $serverId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/events")['data'] ?? [],
            Event::class,
            ['organization_id' => $organizationId, 'server_id' => $serverId]
        );
    }

    /**
     * Get a server event instance.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $eventId
     * @return \Laravel\Forge\Resources\Event
     */
    public function serverEvent($organizationId, $serverId, $eventId)
    {
        return new Event(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/events/{$eventId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Get the output of a server event.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $eventId
     * @return array
     */
    public function serverEventOutput($organizationId, $serverId, $eventId)
    {
        return $this->get("orgs/{$organizationId}/servers/{$serverId}/events/{$eventId}/output");
    }

    /**
     * Get the PHP CLI version.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @return array
     */
    public function phpCliVersion($organizationId, $serverId)
    {
        return $this->get("orgs/{$organizationId}/servers/{$serverId}/php/cli-version");
    }

    /**
     * Update the PHP CLI version.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  array  $data
     * @return array
     */
    public function updatePhpCliVersion($organizationId, $serverId, array $data)
    {
        return $this->put("orgs/{$organizationId}/servers/{$serverId}/php/cli-version", $data);
    }

    /**
     * Get the PHP site version.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @return array
     */
    public function phpSiteVersion($organizationId, $serverId)
    {
        return $this->get("orgs/{$organizationId}/servers/{$serverId}/php/site-version");
    }

    /**
     * Update the PHP site version.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  array  $data
     * @return array
     */
    public function updatePhpSiteVersion($organizationId, $serverId, array $data)
    {
        return $this->put("orgs/{$organizationId}/servers/{$serverId}/php/site-version", $data);
    }

    /**
     * Get the collection of PHP versions.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @return \Laravel\Forge\Resources\PHPVersion[]
     */
    public function phpVersions($organizationId, $serverId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/php/versions")['data'] ?? [],
            PHPVersion::class,
            ['organization_id' => $organizationId, 'server_id' => $serverId]
        );
    }

    /**
     * Install a new PHP version.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  array  $data
     * @return \Laravel\Forge\Resources\PHPVersion
     */
    public function installPhpVersion($organizationId, $serverId, array $data)
    {
        $phpVersion = $this->post("orgs/{$organizationId}/servers/{$serverId}/php/versions", $data)['data'] ?? [];

        return new PHPVersion($phpVersion + ['organization_id' => $organizationId, 'server_id' => $serverId], $this);
    }

    /**
     * Get a PHP version instance.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $phpVersionId
     * @return \Laravel\Forge\Resources\PHPVersion
     */
    public function phpVersion($organizationId, $serverId, $phpVersionId)
    {
        return new PHPVersion(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/php/versions/{$phpVersionId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Update a PHP version.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $phpVersionId
     * @param  array  $data
     * @return \Laravel\Forge\Resources\PHPVersion
     */
    public function updatePhpVersion($organizationId, $serverId, $phpVersionId, array $data)
    {
        $phpVersion = $this->put("orgs/{$organizationId}/servers/{$serverId}/php/versions/{$phpVersionId}", $data)['data'] ?? [];

        return new PHPVersion($phpVersion, $this);
    }

    /**
     * Delete a PHP version.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $phpVersionId
     * @return void
     */
    public function deletePhpVersion($organizationId, $serverId, $phpVersionId)
    {
        $this->delete("orgs/{$organizationId}/servers/{$serverId}/php/versions/{$phpVersionId}");
    }

    /**
     * Get the PHP FPM configuration.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $phpVersionId
     * @return array
     */
    public function phpFpmConfig($organizationId, $serverId, $phpVersionId)
    {
        return $this->get("orgs/{$organizationId}/servers/{$serverId}/php/versions/{$phpVersionId}/configs/fpm");
    }

    /**
     * Update the PHP FPM configuration.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $phpVersionId
     * @param  array  $data
     * @return array
     */
    public function updatePhpFpmConfig($organizationId, $serverId, $phpVersionId, array $data)
    {
        return $this->put("orgs/{$organizationId}/servers/{$serverId}/php/versions/{$phpVersionId}/configs/fpm", $data);
    }

    /**
     * Get the PHP CLI configuration.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $phpVersionId
     * @return array
     */
    public function phpCliConfig($organizationId, $serverId, $phpVersionId)
    {
        return $this->get("orgs/{$organizationId}/servers/{$serverId}/php/versions/{$phpVersionId}/configs/cli");
    }

    /**
     * Update the PHP CLI configuration.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $phpVersionId
     * @param  array  $data
     * @return array
     */
    public function updatePhpCliConfig($organizationId, $serverId, $phpVersionId, array $data)
    {
        return $this->put("orgs/{$organizationId}/servers/{$serverId}/php/versions/{$phpVersionId}/configs/cli", $data);
    }

    /**
     * Get the PHP pool configuration.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $phpVersionId
     * @return array
     */
    public function phpPoolConfig($organizationId, $serverId, $phpVersionId)
    {
        return $this->get("orgs/{$organizationId}/servers/{$serverId}/php/versions/{$phpVersionId}/configs/pool");
    }

    /**
     * Update the PHP pool configuration.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $phpVersionId
     * @param  array  $data
     * @return array
     */
    public function updatePhpPoolConfig($organizationId, $serverId, $phpVersionId, array $data)
    {
        return $this->put("orgs/{$organizationId}/servers/{$serverId}/php/versions/{$phpVersionId}/configs/pool", $data);
    }

    /**
     * Get the PHP max upload size.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @return array
     */
    public function phpMaxUploadSize($organizationId, $serverId)
    {
        return $this->get("orgs/{$organizationId}/servers/{$serverId}/php/max-upload-size");
    }

    /**
     * Update the PHP max upload size.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  array  $data
     * @return array
     */
    public function updatePhpMaxUploadSize($organizationId, $serverId, array $data)
    {
        return $this->put("orgs/{$organizationId}/servers/{$serverId}/php/max-upload-size", $data);
    }

    /**
     * Get the PHP max execution time.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @return array
     */
    public function phpMaxExecutionTime($organizationId, $serverId)
    {
        return $this->get("orgs/{$organizationId}/servers/{$serverId}/php/max-execution-time");
    }

    /**
     * Update the PHP max execution time.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  array  $data
     * @return array
     */
    public function updatePhpMaxExecutionTime($organizationId, $serverId, array $data)
    {
        return $this->put("orgs/{$organizationId}/servers/{$serverId}/php/max-execution-time", $data);
    }

    /**
     * Get the PHP OPcache status.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @return array
     */
    public function phpOpcache($organizationId, $serverId)
    {
        return $this->get("orgs/{$organizationId}/servers/{$serverId}/php/opcache");
    }

    /**
     * Create PHP OPcache configuration.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  array  $data
     * @return array
     */
    public function createPhpOpcache($organizationId, $serverId, array $data)
    {
        return $this->post("orgs/{$organizationId}/servers/{$serverId}/php/opcache", $data);
    }

    /**
     * Delete PHP OPcache configuration.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @return void
     */
    public function deletePhpOpcache($organizationId, $serverId)
    {
        $this->delete("orgs/{$organizationId}/servers/{$serverId}/php/opcache");
    }

    /**
     * Get the collection of team servers.
     *
     * @param  string  $organizationId
     * @param  string  $teamId
     * @return \Laravel\Forge\Resources\Server[]
     */
    public function teamServers($organizationId, $teamId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationId}/teams/{$teamId}/servers")['data'] ?? [],
            Server::class,
            ['organization_id' => $organizationId, 'team_id' => $teamId]
        );
    }

    /**
     * Create a server share with a team.
     *
     * @param  string  $organizationId
     * @param  string  $teamId
     * @param  array  $data
     * @return \Laravel\Forge\Resources\Server
     */
    public function createTeamServerShare($organizationId, $teamId, array $data)
    {
        $server = $this->post("orgs/{$organizationId}/teams/{$teamId}/servers", $data)['data'] ?? [];

        return new Server($server + ['organization_id' => $organizationId, 'team_id' => $teamId], $this);
    }

    /**
     * Delete a server share from a team.
     *
     * @param  string  $organizationId
     * @param  string  $teamId
     * @param  string  $serverId
     * @return void
     */
    public function deleteTeamServerShare($organizationId, $teamId, $serverId)
    {
        $this->delete("orgs/{$organizationId}/teams/{$teamId}/servers/{$serverId}");
    }
}
