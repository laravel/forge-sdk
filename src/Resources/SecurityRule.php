<?php

namespace Themsaid\Forge\Resources;

class SecurityRule extends Resource
{
    /**
     * The id of the rule.
     *
     * @var integer
     */
    public $id;

    /**
     * The id of the server.
     *
     * @var integer
     */
    public $serverId;

    /**
     * The id of the site.
     *
     * @var integer
     */
    public $siteId;

    /**
     * The name of the rule.
     *
     * @var string
     */
    public $name;

    /**
     * The path to route of the rule.
     *
     * @var string
     */
    public $path;

    /**
     * The credentials of the redirect rule.
     *
     * @var string
     */
    public $credentials;

    /**
     * The date/time the rule was created.
     *
     * @var string
     */
    public $createdAt;



    /**
     * Delete the given redirect rule.
     *
     * @return void
     */
    public function delete()
    {
        $this->forge->deleteSecurityRule($this->serverId, $this->siteId, $this->id);
    }
}