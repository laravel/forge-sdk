<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Event;
use Laravel\Forge\Resources\PHPVersion;
use Laravel\Forge\Resources\Server;

trait ManagesServers
{
    /**
     * Get the collection of servers for an organization.
     *
     * @param  string  $organizationSlug
     * @return \Laravel\Forge\Resources\Server[]
     */
    public function servers($organizationSlug)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/servers")['data'] ?? [],
            Server::class,
            ['organization_id' => $organizationSlug]
        );
    }

    /**
     * Get a server instance.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return \Laravel\Forge\Resources\Server
     */
    public function server($organizationSlug, $serverId)
    {
        return new Server(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a new server.
     *
     * @param  string  $organizationSlug
     * @param  bool  $wait
     * @return \Laravel\Forge\Resources\Server
     */
    public function createServer($organizationSlug, array $data, $wait = true)
    {
        $server = $this->post("orgs/{$organizationSlug}/servers", $data)['data'] ?? [];

        if ($wait) {
            return $this->retry($this->getTimeout(), function () use ($organizationSlug, $server) {
                $srv = $this->server($organizationSlug, $server['id']);

                return isset($srv->isReady) && $srv->isReady ? $srv : null;
            });
        }

        return new Server($server + ['organization_id' => $organizationSlug], $this);
    }

    /**
     * Delete a server.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return void
     */
    public function deleteServer($organizationSlug, $serverId)
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}");
    }

    /**
     * Get the collection of archived servers for an organization.
     *
     * @param  string  $organizationSlug
     * @return \Laravel\Forge\Resources\Server[]
     */
    public function archivedServers($organizationSlug)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/servers/archives")['data'] ?? [],
            Server::class,
            ['organization_id' => $organizationSlug]
        );
    }

    /**
     * Create an archived server.
     *
     * @param  string  $organizationSlug
     * @return \Laravel\Forge\Resources\Server
     */
    public function createArchivedServer($organizationSlug, array $data)
    {
        $server = $this->post("orgs/{$organizationSlug}/servers/archives", $data)['data'] ?? [];

        return new Server($server + ['organization_id' => $organizationSlug], $this);
    }

    /**
     * Delete an archived server.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return void
     */
    public function deleteArchivedServer($organizationSlug, $serverId)
    {
        $this->delete("orgs/{$organizationSlug}/servers/archives/{$serverId}");
    }

    /**
     * Create a server action.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return array
     */
    public function createServerAction($organizationSlug, $serverId, array $data)
    {
        return $this->post("orgs/{$organizationSlug}/servers/{$serverId}/actions", $data);
    }

    /**
     * Perform a Nginx service action.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return array
     */
    public function performNginxAction($organizationSlug, $serverId, array $data)
    {
        return $this->post("orgs/{$organizationSlug}/servers/{$serverId}/services/nginx/actions", $data);
    }

    /**
     * Perform a Postgres service action.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return array
     */
    public function performPostgresAction($organizationSlug, $serverId, array $data)
    {
        return $this->post("orgs/{$organizationSlug}/servers/{$serverId}/services/postgres/actions", $data);
    }

    /**
     * Perform a Redis service action.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return array
     */
    public function performRedisAction($organizationSlug, $serverId, array $data)
    {
        return $this->post("orgs/{$organizationSlug}/servers/{$serverId}/services/redis/actions", $data);
    }

    /**
     * Perform a MySQL service action.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return array
     */
    public function performMySQLAction($organizationSlug, $serverId, array $data)
    {
        return $this->post("orgs/{$organizationSlug}/servers/{$serverId}/services/mysql/actions", $data);
    }

    /**
     * Perform a PHP service action.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return array
     */
    public function performPHPAction($organizationSlug, $serverId, array $data)
    {
        return $this->post("orgs/{$organizationSlug}/servers/{$serverId}/services/php/actions", $data);
    }

    /**
     * Perform a Supervisor service action.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return array
     */
    public function performSupervisorAction($organizationSlug, $serverId, array $data)
    {
        return $this->post("orgs/{$organizationSlug}/servers/{$serverId}/services/supervisor/actions", $data);
    }

    /**
     * Get the collection of server events.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return \Laravel\Forge\Resources\Event[]
     */
    public function serverEvents($organizationSlug, $serverId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/events")['data'] ?? [],
            Event::class,
            ['organization_id' => $organizationSlug, 'server_id' => $serverId]
        );
    }

    /**
     * Get a server event instance.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $eventId
     * @return \Laravel\Forge\Resources\Event
     */
    public function serverEvent($organizationSlug, $serverId, $eventId)
    {
        return new Event(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/events/{$eventId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Get the output of a server event.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $eventId
     * @return array
     */
    public function serverEventOutput($organizationSlug, $serverId, $eventId)
    {
        return $this->get("orgs/{$organizationSlug}/servers/{$serverId}/events/{$eventId}/output");
    }

    /**
     * Get the PHP CLI version.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return array
     */
    public function phpCliVersion($organizationSlug, $serverId)
    {
        return $this->get("orgs/{$organizationSlug}/servers/{$serverId}/php/cli-version");
    }

    /**
     * Update the PHP CLI version.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return array
     */
    public function updatePhpCliVersion($organizationSlug, $serverId, array $data)
    {
        return $this->put("orgs/{$organizationSlug}/servers/{$serverId}/php/cli-version", $data);
    }

    /**
     * Get the PHP site version.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return array
     */
    public function phpSiteVersion($organizationSlug, $serverId)
    {
        return $this->get("orgs/{$organizationSlug}/servers/{$serverId}/php/site-version");
    }

    /**
     * Update the PHP site version.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return array
     */
    public function updatePhpSiteVersion($organizationSlug, $serverId, array $data)
    {
        return $this->put("orgs/{$organizationSlug}/servers/{$serverId}/php/site-version", $data);
    }

    /**
     * Get the collection of PHP versions.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return \Laravel\Forge\Resources\PHPVersion[]
     */
    public function phpVersions($organizationSlug, $serverId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/php/versions")['data'] ?? [],
            PHPVersion::class,
            ['organization_id' => $organizationSlug, 'server_id' => $serverId]
        );
    }

    /**
     * Install a new PHP version.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return \Laravel\Forge\Resources\PHPVersion
     */
    public function installPhpVersion($organizationSlug, $serverId, array $data)
    {
        $phpVersion = $this->post("orgs/{$organizationSlug}/servers/{$serverId}/php/versions", $data)['data'] ?? [];

        return new PHPVersion($phpVersion + ['organization_id' => $organizationSlug, 'server_id' => $serverId], $this);
    }

    /**
     * Get a PHP version instance.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $phpVersionId
     * @return \Laravel\Forge\Resources\PHPVersion
     */
    public function phpVersion($organizationSlug, $serverId, $phpVersionId)
    {
        return new PHPVersion(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/php/versions/{$phpVersionId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Update a PHP version.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $phpVersionId
     * @return \Laravel\Forge\Resources\PHPVersion
     */
    public function updatePhpVersion($organizationSlug, $serverId, $phpVersionId, array $data)
    {
        $phpVersion = $this->put("orgs/{$organizationSlug}/servers/{$serverId}/php/versions/{$phpVersionId}", $data)['data'] ?? [];

        return new PHPVersion($phpVersion, $this);
    }

    /**
     * Delete a PHP version.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $phpVersionId
     * @return void
     */
    public function deletePhpVersion($organizationSlug, $serverId, $phpVersionId)
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/php/versions/{$phpVersionId}");
    }

    /**
     * Get the PHP FPM configuration.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $phpVersionId
     * @return array
     */
    public function phpFpm($organizationSlug, $serverId, $phpVersionId)
    {
        return $this->get("orgs/{$organizationSlug}/servers/{$serverId}/php/versions/{$phpVersionId}/configs/fpm");
    }

    /**
     * Update the PHP FPM configuration.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $phpVersionId
     * @return array
     */
    public function updatePhpFpm($organizationSlug, $serverId, $phpVersionId, array $data)
    {
        return $this->put("orgs/{$organizationSlug}/servers/{$serverId}/php/versions/{$phpVersionId}/configs/fpm", $data);
    }

    /**
     * Get the PHP CLI configuration.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $phpVersionId
     * @return array
     */
    public function phpCli($organizationSlug, $serverId, $phpVersionId)
    {
        return $this->get("orgs/{$organizationSlug}/servers/{$serverId}/php/versions/{$phpVersionId}/configs/cli");
    }

    /**
     * Update the PHP CLI configuration.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $phpVersionId
     * @return array
     */
    public function updatePhpCli($organizationSlug, $serverId, $phpVersionId, array $data)
    {
        return $this->put("orgs/{$organizationSlug}/servers/{$serverId}/php/versions/{$phpVersionId}/configs/cli", $data);
    }

    /**
     * Get the PHP pool configuration.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $phpVersionId
     * @return array
     */
    public function phpPool($organizationSlug, $serverId, $phpVersionId)
    {
        return $this->get("orgs/{$organizationSlug}/servers/{$serverId}/php/versions/{$phpVersionId}/configs/pool");
    }

    /**
     * Update the PHP pool configuration.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $phpVersionId
     * @return array
     */
    public function updatePhpPool($organizationSlug, $serverId, $phpVersionId, array $data)
    {
        return $this->put("orgs/{$organizationSlug}/servers/{$serverId}/php/versions/{$phpVersionId}/configs/pool", $data);
    }

    /**
     * Get the PHP max upload size.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return array
     */
    public function phpMaxUploadSize($organizationSlug, $serverId)
    {
        return $this->get("orgs/{$organizationSlug}/servers/{$serverId}/php/max-upload-size");
    }

    /**
     * Update the PHP max upload size.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return array
     */
    public function updatePhpMaxUploadSize($organizationSlug, $serverId, array $data)
    {
        return $this->put("orgs/{$organizationSlug}/servers/{$serverId}/php/max-upload-size", $data);
    }

    /**
     * Get the PHP max execution time.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return array
     */
    public function phpMaxExecutionTime($organizationSlug, $serverId)
    {
        return $this->get("orgs/{$organizationSlug}/servers/{$serverId}/php/max-execution-time");
    }

    /**
     * Update the PHP max execution time.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return array
     */
    public function updatePhpMaxExecutionTime($organizationSlug, $serverId, array $data)
    {
        return $this->put("orgs/{$organizationSlug}/servers/{$serverId}/php/max-execution-time", $data);
    }

    /**
     * Get the PHP OPcache status.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return array
     */
    public function phpOpcache($organizationSlug, $serverId)
    {
        return $this->get("orgs/{$organizationSlug}/servers/{$serverId}/php/opcache");
    }

    /**
     * Create PHP OPcache configuration.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return array
     */
    public function createPhpOpcache($organizationSlug, $serverId, array $data)
    {
        return $this->post("orgs/{$organizationSlug}/servers/{$serverId}/php/opcache", $data);
    }

    /**
     * Delete PHP OPcache configuration.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return void
     */
    public function deletePhpOpcache($organizationSlug, $serverId)
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/php/opcache");
    }

    /**
     * Get the collection of team servers.
     *
     * @param  string  $organizationSlug
     * @param  string  $teamId
     * @return \Laravel\Forge\Resources\Server[]
     */
    public function teamServers($organizationSlug, $teamId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/teams/{$teamId}/servers")['data'] ?? [],
            Server::class,
            ['organization_id' => $organizationSlug, 'team_id' => $teamId]
        );
    }

    /**
     * Share a server with a team (create team servers share).
     *
     * @param  string  $organizationSlug
     * @param  string  $teamId
     * @return \Laravel\Forge\Resources\Server
     */
    public function createTeamServersShare($organizationSlug, $teamId, array $data)
    {
        $server = $this->post("orgs/{$organizationSlug}/teams/{$teamId}/servers", $data)['data'] ?? [];

        return new Server($server + ['organization_id' => $organizationSlug, 'team_id' => $teamId], $this);
    }

    /**
     * Remove a server share from a team (delete team servers share).
     *
     * @param  string  $organizationSlug
     * @param  string  $teamId
     * @param  string  $serverId
     * @return void
     */
    public function deleteTeamServersShare($organizationSlug, $teamId, $serverId)
    {
        $this->delete("orgs/{$organizationSlug}/teams/{$teamId}/servers/{$serverId}");
    }
}
