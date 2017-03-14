<?php

namespace Laravel\Forge\Resources;

class Credential extends Resource
{
    /**
     * The id of the credential.
     *
     * @var integer
     */
    public $id;

    /**
     * The name of the credential.
     *
     * @var string
     */
    public $name;

    /**
     * The type of the credential.
     *
     * @var string
     */
    public $type;
}