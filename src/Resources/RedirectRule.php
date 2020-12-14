<?php

namespace Laravel\Forge\Resources;

class RedirectRule extends Resource
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
     * The id of the site.
     *
     * @var int
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
        $this->forge->deleteRedirectRule($this->serverId, $this->siteId, $this->id);
    }
}
