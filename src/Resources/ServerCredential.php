<?php

namespace Laravel\Forge\Resources;

class ServerCredential extends Resource
{
    /**
     * The id of the server credential.
     *
     * @var int
     */
    public $id;

    /**
     * The id of the organization.
     *
     * @var int
     */
    public $organizationId;

    /**
     * The name of the server credential.
     *
     * @var string
     */
    public $name;

    /**
     * The type of the server credential.
     *
     * @var string
     */
    public $type;

    /**
     * The provider of the server credential.
     *
     * @var string
     */
    public $provider;

    /**
     * The date/time the server credential was created.
     *
     * @var string
     */
    public $createdAt;
}
