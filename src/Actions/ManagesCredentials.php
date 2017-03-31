<?php

namespace Themsaid\Forge\Actions;

use Themsaid\Forge\Resources\Credential;

trait ManagesCredentials
{
    /**
     * Get the collection of recipes.
     *
     * @return Credential[]
     */
    public function credentials()
    {
        return $this->transformCollection(
            $this->get('credentials')['credentials'], Credential::class
        );
    }
}