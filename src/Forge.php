<?php

declare(strict_types=1);

namespace Laravel\Forge;

use GuzzleHttp\Client as HttpClient;

class Forge
{
    use Actions\ManagesBackgroundProcesses,
        Actions\ManagesBackups,
        Actions\ManagesCommands,
        Actions\ManagesDatabases,
        Actions\ManagesDeployments,
        Actions\ManagesFirewallRules,
        Actions\ManagesIntegrations,
        Actions\ManagesLogs,
        Actions\ManagesMonitors,
        Actions\ManagesNginx,
        Actions\ManagesOrganizations,
        Actions\ManagesProviders,
        Actions\ManagesRecipes,
        Actions\ManagesRedirectRules,
        Actions\ManagesRoles,
        Actions\ManagesScheduledJobs,
        Actions\ManagesSecurityRules,
        Actions\ManagesServerCredentials,
        Actions\ManagesServers,
        Actions\ManagesSites,
        Actions\ManagesSSHKeys,
        Actions\ManagesStorageProviders,
        Actions\ManagesTeams,
        Actions\ManagesUser,
        MakesHttpRequests;

    /**
     * The Forge API Key.
     */
    protected string $apiKey;

    /**
     * The Guzzle HTTP Client instance.
     */
    public HttpClient $guzzle;

    /**
     * Number of seconds a request is retried.
     */
    public int $timeout = 30;

    /**
     * Create a new Forge instance.
     */
    public function __construct(?string $apiKey = null, ?HttpClient $guzzle = null)
    {
        if (! is_null($apiKey)) {
            $this->setApiKey($apiKey, $guzzle);
        }

        if (! is_null($guzzle)) {
            $this->guzzle = $guzzle;
        }
    }

    /**
     * Transform the items of the collection to the given class.
     */
    public function transformCollection(
        array $collection,
        string $class,
        ?string $organizationSlug = null,
        ?int $serverId = null,
        ?int $siteId = null,
        array $extra = [],
    ): array {
        $context = array_filter([
            'organization_slug' => $organizationSlug,
            'server_id' => $serverId,
            'site_id' => $siteId,
        ], fn ($v) => ! is_null($v));

        $extraData = $context + $extra;

        return array_map(function ($data) use ($class, $extraData) {
            return new $class($data + $extraData, $this);
        }, $collection);
    }

    /**
     * Create a new resource instance with context data.
     *
     * Convention: action-trait methods whose OpenAPI operation declares a
     * non-empty response body return the hydrated resource via this helper
     * (e.g. createServer, updateBackupConfiguration). Methods whose endpoint
     * is documented as empty (204 no content, or 202 with no schema) stay
     * `: void` — confirmed-empty deletes/reboots/toggles such as deleteServer,
     * disableQuickDeploy, updatePhpCliVersion, updateSiteEnvironment.
     *
     * @template TResource of \Laravel\Forge\Resources\Resource
     *
     * @param  class-string<TResource>  $class
     * @return TResource
     */
    protected function newResource(
        string $class,
        array $data,
        ?string $organizationSlug = null,
        ?int $serverId = null,
        ?int $siteId = null,
        array $extra = [],
    ): mixed {
        $context = array_filter([
            'organization_slug' => $organizationSlug,
            'server_id' => $serverId,
            'site_id' => $siteId,
        ], fn ($v) => ! is_null($v));

        return new $class($data + $context + $extra, $this);
    }

    /**
     * Make a paginated GET request and return a CursorPaginator of resource objects.
     */
    protected function paginatedCollection(
        string $uri,
        string $class,
        ?string $organizationSlug = null,
        ?int $serverId = null,
        ?int $siteId = null,
        array $extra = [],
        array $query = [],
    ): CursorPaginator {
        $response = $this->get($uri, $query);

        $data = $response['data'] ?? [];
        $meta = $response['meta'] ?? [];

        $items = $this->transformCollection(
            $data,
            $class,
            $organizationSlug,
            $serverId,
            $siteId,
            $extra,
        );

        return new CursorPaginator(
            items: $items,
            nextCursor: $meta['next_cursor'] ?? null,
            perPage: $meta['per_page'] ?? null,
            forge: $this,
            uri: $uri,
            class: $class,
            organizationSlug: $organizationSlug,
            serverId: $serverId,
            siteId: $siteId,
            extra: $extra,
            query: $query,
        );
    }

    /**
     * Set the api key and setup the guzzle request object.
     */
    public function setApiKey(string $apiKey, ?HttpClient $guzzle = null): static
    {
        $this->apiKey = $apiKey;

        $this->guzzle = $guzzle ?: new HttpClient([
            'base_uri' => 'https://forge.laravel.com/api/',
            'http_errors' => false,
            'headers' => [
                'Authorization' => 'Bearer '.$this->apiKey,
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
                'User-Agent' => 'Laravel Forge PHP/4.0',
            ],
        ]);

        return $this;
    }

    /**
     * Set a new timeout.
     */
    public function setTimeout(int $timeout): static
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Get the timeout.
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

}
