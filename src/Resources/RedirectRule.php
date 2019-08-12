<?php

namespace Themsaid\Forge\Resources;

class RedirectRule extends Resource
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
     * The from route of the rule.
     *
     * @var string
     */
    public $from;

    /**
     * The to route of the rule.
     *
     * @var string
     */
    public $to;

    /**
     * The type of the redirect rule.
     *
     * @var string
     */
    public $type;

    /**
     * The status of the redirect rule.
     *
     * @var string
     */
    public $status;

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
        // $this->forge->deleteFirewallRule($this->serverId, $this->id);
    }
}