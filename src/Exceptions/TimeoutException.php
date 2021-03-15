<?php

namespace Laravel\Forge\Exceptions;

use Exception;

class TimeoutException extends Exception
{
    /**
     * The output returned from the operation.
     *
     * @var array|null
     */
    public $output;

    /**
     * Create a new exception instance.
     *
     * @param  array|null  $output
     * @return void
     */
    public function __construct(array $output = null)
    {
        parent::__construct('Script timed out while waiting for the process to complete.');

        $this->output = $output;
    }

    /**
     * The output returned from the operation.
     *
     * @return array|null
     */
    public function output()
    {
        return $this->output;
    }
}
