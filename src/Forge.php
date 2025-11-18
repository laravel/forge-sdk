<?php

namespace Laravel\Forge;

use GuzzleHttp\Client as HttpClient;
use Laravel\Forge\Resources\User;

class Forge
{
    use Actions\ManagesBackgroundProcesses,
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
        Actions\ManagesTeams,
        Actions\ManagesUser,
        MakesHttpRequests;

    /**
     * The Forge API Key.
     *
     * @var string
     */
    protected $apiKey;

    /**
     * The Guzzle HTTP Client instance.
     *
     * @var \GuzzleHttp\Client
     */
    public $guzzle;

    /**
     * Number of seconds a request is retried.
     *
     * @var int
     */
    public $timeout = 30;

    /**
     * Create a new Forge instance.
     *
     * @return void
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
     *
     * @param  array  $collection
     * @param  string  $class
     * @param  array  $extraData
     * @return array
     */
    protected function transformCollection($collection, $class, $extraData = [])
    {
        return array_map(function ($data) use ($class, $extraData) {
            return new $class($data + $extraData, $this);
        }, $collection);
    }

    /**
     * Set the api key and setup the guzzle request object.
     *
     * @param  \GuzzleHttp\Client|null  $guzzle
     * @return $this
     */
    public function setApiKey(string $apiKey, $guzzle = null)
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
     *
     * @param  int  $timeout
     * @return $this
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Get the timeout.
     *
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Get an authenticated user instance.
     *
     * @return \Laravel\Forge\Resources\User
     */
    public function user()
    {
        return new User($this->get('user')['data'] ?? []);
    }

    /**
     * Get "me" user instance (alias for user()).
     *
     * @return \Laravel\Forge\Resources\User
     */
    public function me()
    {
        return new User($this->get('me')['data'] ?? []);
    }
}
