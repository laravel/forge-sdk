<?php

declare(strict_types=1);

namespace Laravel\Forge\Exceptions;

use Exception;

class RateLimitExceededException extends Exception
{
    /**
     * The timestamp that the rate limit will be reset.
     */
    public ?int $rateLimitResetsAt;

    /**
     * Create a new exception instance.
     */
    public function __construct(?int $rateLimitReset)
    {
        parent::__construct('Too Many Requests.');

        $this->rateLimitResetsAt = $rateLimitReset;
    }
}
