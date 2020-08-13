<?php

namespace Laravel\Forge\Resources;

class FirewallRule extends Resource
{
    /**
     * The id of the rule.
     *
     * @var int
     */
    public $id;

    /**
     * The id of the server.
     *
     * @var int
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
     * @var int
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
