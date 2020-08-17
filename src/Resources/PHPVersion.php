<?php

namespace Laravel\Forge\Resources;

class PHPVersion extends Resource
{
    /**
     * The id of the PHP version.
     *
     * @var int
     */
    public $id;

    /**
     * The version of PHP.
     *
     * @var string
     */
    public $version;

    /**
     * The status of the version.
     *
     * @var string
     */
    public $status;

    /**
     * The binary name of PHP.
     *
     * @var string
     */
    public $binaryName;

    /**
     * The displayable version of PHP.
     *
     * @var string
     */
    public $displayableVersion;

    /**
     * Whether the version is used as the default when creating a new site.
     *
     * @var bool
     */
    public $usedAsDefault;

    /**
     * Whether the version is used on the CLI by default.
     *
     * @var bool
     */
    public $usedOnCli;
}
