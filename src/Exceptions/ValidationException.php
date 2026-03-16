<?php

declare(strict_types=1);

namespace Laravel\Forge\Exceptions;

use Exception;

class ValidationException extends Exception
{
    /**
     * The array of errors.
     */
    public array $errors;

    /**
     * Create a new exception instance.
     */
    public function __construct(array $errors)
    {
        parent::__construct('The given data failed to pass validation.');

        $this->errors = $errors;
    }

    /**
     * The array of errors.
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
