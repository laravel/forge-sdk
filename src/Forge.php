<?php

namespace Laravel\Forge;

use GuzzleHttp\Client as HttpClient;

class Forge
{
    use MakesHttpRequests,
        Actions\ManagesJobs,
        Actions\ManagesSites,
        Actions\ManagesServers,
        Actions\ManagesDaemons,
        Actions\ManagesWorkers,
        Actions\ManagesSSHKeys,
        Actions\ManagesRecipes,
        Actions\ManagesMysqlUsers,
        Actions\ManagesCredentials,
        Actions\ManagesCertificates,
        Actions\ManagesFirewallRules,
        Actions\ManagesMysqlDatabases;

    /**
     * The Forge API Key.
     *
     * @var string
     */
    public $apiKey;

    /**
     * The Guzzle HTTP Client instance.
     *
     * @var \GuzzleHttp\Client
     */
    public $guzzle;

    /**
     * Create a new Forge instance.
     *
     * @param  string $apiKey
     * @param  \GuzzleHttp\Client $guzzle
     * @return void
     */
    public function __construct($apiKey, HttpClient $guzzle = null)
    {
        $this->apiKey = $apiKey;

        $this->guzzle = $guzzle ?: new HttpClient([
            'base_uri' => 'https://forge.laravel.com/api/v1/',
            'http_errors' => false,
            'headers' => [
                'Authorization' => 'Bearer '.$this->apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);
    }

    /**
     * Transform the items of the collection to the given class.
     *
     * @param  array $collection
     * @param  string $class
     * @param  array $extraData
     * @return array
     */
    protected function transformCollection($collection, $class, $extraData = [])
    {
        return array_map(function ($data) use ($class, $extraData) {
            return new $class($data + $extraData, $this);
        }, $collection);
    }
}