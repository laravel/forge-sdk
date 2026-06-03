<?php

namespace Laravel\Forge;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Traits\ForwardsCalls;

/**
 * @mixin Forge
 */
class ForgeManager
{
    use ForwardsCalls;

    /**
     * The Forge instance.
     *
     * @var Forge
     */
    protected $forge;

    /**
     * Create a new Forge manager instance.
     *
     * @param  string  $token
     */
    public function __construct($token, ?HttpClient $guzzle = null)
    {
        $this->forge = new Forge($token, $guzzle);
    }

    /**
     * Dynamically pass methods to the Forge instance.
     *
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return $this->forwardCallTo($this->forge, $method, $parameters);
    }
}
