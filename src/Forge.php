<?php

namespace Themsaid\Forge;

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
     * Number of seconds a request is retried.
     *
     * @var int
     */
    public $timeout = 30;

    /**
     * Create a new Forge instance.
     *
     * @param  string $apiKey
     * @param  \GuzzleHttp\Client $guzzle
     * @return void
     */
    public function __construct($apiKey = null, HttpClient $guzzle = null)
    {
        if (! is_null($apiKey)) {
            $this->setApiKey($apiKey, $guzzle);
        }
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
    
    /**
     * Set the api key and setup the guzzle request object
     * 
     * @param string $apiKey
     * @return $this
     */
    public function setApiKey($apiKey, $guzzle)
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
        
        return $this;
    }

    /**
     * Set a new timeout
     *
     * @param  int $timeout
     * @return $this
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Get the timeout
     *
     * @return  int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }
}
