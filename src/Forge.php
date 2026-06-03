<?php

namespace Laravel\Forge;

use GuzzleHttp\Client;
use GuzzleHttp\Client as HttpClient;
use Laravel\Forge\Resources\User;

class Forge
{
    use Actions\ManagesBackups,
        Actions\ManagesCertificates,
        Actions\ManagesCredentials,
        Actions\ManagesDaemons,
        Actions\ManagesDatabases,
        Actions\ManagesDatabaseUsers,
        Actions\ManagesFirewallRules,
        Actions\ManagesJobs,
        Actions\ManagesMonitors,
        Actions\ManagesNginxTemplates,
        Actions\ManagesRecipes,
        Actions\ManagesRedirectRules,
        Actions\ManagesSecurityRules,
        Actions\ManagesServers,
        Actions\ManagesSiteCommands,
        Actions\ManagesSites,
        Actions\ManagesSSHKeys,
        Actions\ManagesWebhooks,
        Actions\ManagesWorkers,
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
     * @var Client
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
     * @param  Client|null  $guzzle
     * @return $this
     */
    public function setApiKey(string $apiKey, $guzzle = null)
    {
        $this->apiKey = $apiKey;

        $this->guzzle = $guzzle ?: new HttpClient([
            'base_uri' => 'https://forge.laravel.com/api/v1/',
            'http_errors' => false,
            'headers' => [
                'Authorization' => 'Bearer '.$this->apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'User-Agent' => 'Laravel Forge PHP/3.0',
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
     * @return User
     */
    public function user()
    {
        return new User($this->get('user')['user']);
    }
}
