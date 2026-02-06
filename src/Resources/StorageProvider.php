<?php

namespace Laravel\Forge\Resources;

class StorageProvider extends Resource
{
    /**
     * The id of the storage provider.
     *
     * @var int
     */
    public $id;

    /**
     * The id of the organization.
     *
     * @var string
     */
    public $organizationId;

    /**
     * The name of the storage provider.
     *
     * @var string
     */
    public $name;

    /**
     * The type of the storage provider.
     *
     * @var string
     */
    public $type;

    /**
     * The date/time the storage provider was created.
     *
     * @var string
     */
    public $createdAt;
}
