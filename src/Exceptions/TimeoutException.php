<?php

declare(strict_types=1);

namespace Laravel\Forge\Exceptions;

use Exception;

class TimeoutException extends Exception
{
    /**
     * The output returned from the operation.
     */
    public array $output;

    /**
     * Create a new exception instance.
     */
    public function __construct(array $output)
    {
        parent::__construct('Script timed out while waiting for the process to complete.');

        $this->output = $output;
    }

    /**
     * The output returned from the operation.
     */
    public function output(): array
    {
        return $this->output;
    }
}
