<?php

namespace Laravel\Forge\Resources;

class PredefinedRole extends Resource
{
    /**
     * The id of the predefined role.
     *
     * @var int
     */
    public $id;

    /**
     * The name of the predefined role.
     *
     * @var string
     */
    public $name;

    /**
     * The description of the predefined role.
     *
     * @var string
     */
    public $description;
}
