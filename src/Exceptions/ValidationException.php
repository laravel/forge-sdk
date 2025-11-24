<?php

namespace Laravel\Forge\Exceptions;

use Exception;

class ValidationException extends Exception
{
    /**
     * The array of errors.
     *
     * @var array
     */
    public $errors;

    /**
     * Create a new exception instance.
     *
     * @return void
     */
    public function __construct(array $errors)
    {
        parent::__construct($this->resolveMessage($errors));

        $this->errors = $errors;
    }

    /**
     * The array of errors.
     *
     * @return array
     */
    public function errors()
    {
        return $this->errors;
    }

    /**
     * Resolve the message based on the provided errors.
     *
     * @param  array  $errors  An array containing validation errors.
     * @return string The resolved message string.
     */
    private function resolveMessage(array $errors): string
    {
        if (empty($errors)) {
            return 'The given data failed to pass validation.';
        }

        return current(current($errors));
    }
}
