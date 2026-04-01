<?php

declare(strict_types=1);

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
     */
    protected Forge $forge;

    /**
     * Create a new Forge manager instance.
     */
    public function __construct(string $token, ?HttpClient $guzzle = null)
    {
        $this->forge = new Forge($token, $guzzle);
    }

    /**
     * Dynamically pass methods to the Forge instance.
     */
    public function __call(string $method, array $parameters): mixed
    {
        return $this->forwardCallTo($this->forge, $method, $parameters);
    }
}
