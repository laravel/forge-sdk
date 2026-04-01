<?php

declare(strict_types=1);

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Event;
use Laravel\Forge\Resources\PHPVersion;
use Laravel\Forge\Resources\Server;

trait ManagesServers
{
    /**
     * Get the collection of servers for an organization.
     *
     * @return Server[]
     */
    public function servers(string $organizationSlug): array
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/servers")['data'] ?? [],
            Server::class,
            $organizationSlug,
        );
    }

    /**
     * Get a server instance.
     */
    public function server(string $organizationSlug, int $serverId): Server
    {
        return $this->newResource(
            Server::class,
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}")['data'] ?? [],
            $organizationSlug,
        );
    }

    /**
     * Create a new server.
     */
    public function createServer(string $organizationSlug, array $data, bool $wait = true): Server
    {
        $server = $this->post("orgs/{$organizationSlug}/servers", $data)['data'] ?? [];

        if ($wait) {
            return $this->retry($this->getTimeout(), function () use ($organizationSlug, $server) {
                $srv = $this->server($organizationSlug, $server['id']);

                return isset($srv->isReady) && $srv->isReady ? $srv : null;
            });
        }

        return $this->newResource(Server::class, $server, $organizationSlug);
    }

    /**
     * Delete a server.
     */
    public function deleteServer(string $organizationSlug, int $serverId): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}");
    }

    /**
     * Get the collection of archived servers for an organization.
     *
     * @return Server[]
     */
    public function archivedServers(string $organizationSlug): array
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/servers/archives")['data'] ?? [],
            Server::class,
            $organizationSlug,
        );
    }

    /**
     * Create an archived server.
     */
    public function createArchivedServer(string $organizationSlug, array $data): Server
    {
        $server = $this->post("orgs/{$organizationSlug}/servers/archives", $data)['data'] ?? [];

        return $this->newResource(Server::class, $server, $organizationSlug);
    }

    /**
     * Delete an archived server.
     */
    public function deleteArchivedServer(string $organizationSlug, int $serverId): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/archives/{$serverId}");
    }

    /**
     * Create a server action.
     */
    public function createServerAction(string $organizationSlug, int $serverId, array $data): array
    {
        return $this->post("orgs/{$organizationSlug}/servers/{$serverId}/actions", $data);
    }

    /**
     * Perform a Nginx service action.
     */
    public function performNginxAction(string $organizationSlug, int $serverId, array $data): array
    {
        return $this->post("orgs/{$organizationSlug}/servers/{$serverId}/services/nginx/actions", $data);
    }

    /**
     * Perform a Postgres service action.
     */
    public function performPostgresAction(string $organizationSlug, int $serverId, array $data): array
    {
        return $this->post("orgs/{$organizationSlug}/servers/{$serverId}/services/postgres/actions", $data);
    }

    /**
     * Perform a Redis service action.
     */
    public function performRedisAction(string $organizationSlug, int $serverId, array $data): array
    {
        return $this->post("orgs/{$organizationSlug}/servers/{$serverId}/services/redis/actions", $data);
    }

    /**
     * Perform a MySQL service action.
     */
    public function performMySQLAction(string $organizationSlug, int $serverId, array $data): array
    {
        return $this->post("orgs/{$organizationSlug}/servers/{$serverId}/services/mysql/actions", $data);
    }

    /**
     * Perform a PHP service action.
     */
    public function performPHPAction(string $organizationSlug, int $serverId, array $data): array
    {
        return $this->post("orgs/{$organizationSlug}/servers/{$serverId}/services/php/actions", $data);
    }

    /**
     * Perform a Supervisor service action.
     */
    public function performSupervisorAction(string $organizationSlug, int $serverId, array $data): array
    {
        return $this->post("orgs/{$organizationSlug}/servers/{$serverId}/services/supervisor/actions", $data);
    }

    /**
     * Get the collection of server events.
     *
     * @return Event[]
     */
    public function serverEvents(string $organizationSlug, int $serverId): array
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/events")['data'] ?? [],
            Event::class,
            $organizationSlug,
            $serverId,
        );
    }

    /**
     * Get a server event instance.
     */
    public function serverEvent(string $organizationSlug, int $serverId, int $eventId): Event
    {
        return $this->newResource(
            Event::class,
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/events/{$eventId}")['data'] ?? [],
            $organizationSlug,
            $serverId,
        );
    }

    /**
     * Get the output of a server event.
     */
    public function serverEventOutput(string $organizationSlug, int $serverId, int $eventId): array
    {
        return $this->get("orgs/{$organizationSlug}/servers/{$serverId}/events/{$eventId}/output");
    }

    /**
     * Get the PHP CLI version.
     */
    public function phpCliVersion(string $organizationSlug, int $serverId): array
    {
        return $this->get("orgs/{$organizationSlug}/servers/{$serverId}/php/cli-version");
    }

    /**
     * Update the PHP CLI version.
     */
    public function updatePhpCliVersion(string $organizationSlug, int $serverId, array $data): array
    {
        return $this->put("orgs/{$organizationSlug}/servers/{$serverId}/php/cli-version", $data);
    }

    /**
     * Get the PHP site version.
     */
    public function phpSiteVersion(string $organizationSlug, int $serverId): array
    {
        return $this->get("orgs/{$organizationSlug}/servers/{$serverId}/php/site-version");
    }

    /**
     * Update the PHP site version.
     */
    public function updatePhpSiteVersion(string $organizationSlug, int $serverId, array $data): array
    {
        return $this->put("orgs/{$organizationSlug}/servers/{$serverId}/php/site-version", $data);
    }

    /**
     * Get the collection of PHP versions.
     *
     * @return PHPVersion[]
     */
    public function phpVersions(string $organizationSlug, int $serverId): array
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/php/versions")['data'] ?? [],
            PHPVersion::class,
            $organizationSlug,
            $serverId,
        );
    }

    /**
     * Install a new PHP version.
     */
    public function installPhpVersion(string $organizationSlug, int $serverId, array $data): PHPVersion
    {
        return $this->newResource(
            PHPVersion::class,
            $this->post("orgs/{$organizationSlug}/servers/{$serverId}/php/versions", $data)['data'] ?? [],
            $organizationSlug,
            $serverId,
        );
    }

    /**
     * Get a PHP version instance.
     */
    public function phpVersion(string $organizationSlug, int $serverId, int $phpVersionId): PHPVersion
    {
        return $this->newResource(
            PHPVersion::class,
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/php/versions/{$phpVersionId}")['data'] ?? [],
            $organizationSlug,
            $serverId,
        );
    }

    /**
     * Update a PHP version.
     */
    public function updatePhpVersion(string $organizationSlug, int $serverId, int $phpVersionId, array $data): PHPVersion
    {
        return $this->newResource(
            PHPVersion::class,
            $this->put("orgs/{$organizationSlug}/servers/{$serverId}/php/versions/{$phpVersionId}", $data)['data'] ?? [],
            $organizationSlug,
            $serverId,
        );
    }

    /**
     * Delete a PHP version.
     */
    public function deletePhpVersion(string $organizationSlug, int $serverId, int $phpVersionId): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/php/versions/{$phpVersionId}");
    }

    /**
     * Get the PHP FPM configuration.
     */
    public function phpFpm(string $organizationSlug, int $serverId, int $phpVersionId): string
    {
        $response = $this->get("orgs/{$organizationSlug}/servers/{$serverId}/php/versions/{$phpVersionId}/configs/fpm");

        return $response['data']['attributes']['configuration'] ?? '';
    }

    /**
     * Update the PHP FPM configuration.
     */
    public function updatePhpFpm(string $organizationSlug, int $serverId, int $phpVersionId, array $data): array
    {
        return $this->put("orgs/{$organizationSlug}/servers/{$serverId}/php/versions/{$phpVersionId}/configs/fpm", $data);
    }

    /**
     * Get the PHP CLI configuration.
     */
    public function phpCli(string $organizationSlug, int $serverId, int $phpVersionId): string
    {
        $response = $this->get("orgs/{$organizationSlug}/servers/{$serverId}/php/versions/{$phpVersionId}/configs/cli");

        return $response['data']['attributes']['configuration'] ?? '';
    }

    /**
     * Update the PHP CLI configuration.
     */
    public function updatePhpCli(string $organizationSlug, int $serverId, int $phpVersionId, array $data): array
    {
        return $this->put("orgs/{$organizationSlug}/servers/{$serverId}/php/versions/{$phpVersionId}/configs/cli", $data);
    }

    /**
     * Get the PHP pool configuration.
     */
    public function phpPool(string $organizationSlug, int $serverId, int $phpVersionId): string
    {
        $response = $this->get("orgs/{$organizationSlug}/servers/{$serverId}/php/versions/{$phpVersionId}/configs/pool");

        return $response['data']['attributes']['configuration'] ?? '';
    }

    /**
     * Update the PHP pool configuration.
     */
    public function updatePhpPool(string $organizationSlug, int $serverId, int $phpVersionId, array $data): array
    {
        return $this->put("orgs/{$organizationSlug}/servers/{$serverId}/php/versions/{$phpVersionId}/configs/pool", $data);
    }

    /**
     * Get the PHP max upload size.
     */
    public function phpMaxUploadSize(string $organizationSlug, int $serverId): array
    {
        return $this->get("orgs/{$organizationSlug}/servers/{$serverId}/php/max-upload-size");
    }

    /**
     * Update the PHP max upload size.
     */
    public function updatePhpMaxUploadSize(string $organizationSlug, int $serverId, array $data): array
    {
        return $this->put("orgs/{$organizationSlug}/servers/{$serverId}/php/max-upload-size", $data);
    }

    /**
     * Get the PHP max execution time.
     */
    public function phpMaxExecutionTime(string $organizationSlug, int $serverId): array
    {
        return $this->get("orgs/{$organizationSlug}/servers/{$serverId}/php/max-execution-time");
    }

    /**
     * Update the PHP max execution time.
     */
    public function updatePhpMaxExecutionTime(string $organizationSlug, int $serverId, array $data): array
    {
        return $this->put("orgs/{$organizationSlug}/servers/{$serverId}/php/max-execution-time", $data);
    }

    /**
     * Get the PHP OPcache status.
     */
    public function phpOpcache(string $organizationSlug, int $serverId): array
    {
        return $this->get("orgs/{$organizationSlug}/servers/{$serverId}/php/opcache");
    }

    /**
     * Create PHP OPcache configuration.
     */
    public function createPhpOpcache(string $organizationSlug, int $serverId, array $data): array
    {
        return $this->post("orgs/{$organizationSlug}/servers/{$serverId}/php/opcache", $data);
    }

    /**
     * Delete PHP OPcache configuration.
     */
    public function deletePhpOpcache(string $organizationSlug, int $serverId): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/php/opcache");
    }

    /**
     * Get the collection of team servers.
     *
     * @return Server[]
     */
    public function teamServers(string $organizationSlug, int $teamId): array
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/teams/{$teamId}/servers")['data'] ?? [],
            Server::class,
            $organizationSlug,
            extra: ['team_id' => $teamId],
        );
    }

    /**
     * Share a server with a team (create team servers share).
     */
    public function createTeamServersShare(string $organizationSlug, int $teamId, array $data): Server
    {
        $server = $this->post("orgs/{$organizationSlug}/teams/{$teamId}/servers", $data)['data'] ?? [];

        return $this->newResource(Server::class, $server, $organizationSlug, extra: ['team_id' => $teamId]);
    }

    /**
     * Remove a server share from a team (delete team servers share).
     */
    public function deleteTeamServersShare(string $organizationSlug, int $teamId, int $serverId): void
    {
        $this->delete("orgs/{$organizationSlug}/teams/{$teamId}/servers/{$serverId}");
    }
}
