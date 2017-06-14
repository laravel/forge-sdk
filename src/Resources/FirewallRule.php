<?php

namespace Themsaid\Forge\Resources;

class FirewallRule extends Resource
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
     * The name of the rule.
     *
     * @var string
     */
    public $name;

    /**
     * The port number used.
     *
     * @var integer
     */
    public $port;

    /**
     * The IP Address.
     *
     * @var string
     */
    public $ipAddress;

    /**
     * The status of the rule.
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
     * Delete the given firewall rule.
     *
     * @return void
     */
    public function delete()
    {
        $this->forge->deleteFirewallRule($this->serverId, $this->id);
    }
}
