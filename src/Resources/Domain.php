<?php

namespace Laravel\Forge\Resources;

class Domain extends Resource
{
    /**
     * The id of the domain.
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
     * The domain name.
     *
     * @var string
     */
    public $name;

    /**
     * The status of the domain.
     *
     * @var string
     */
    public $status;

    /**
     * The type of the domain.
     *
     * @var string
     */
    public $type;

    /**
     * The date/time the domain was created.
     *
     * @var string
     */
    public $createdAt;

    /**
     * Delete the given domain.
     *
     * @return void
     */
    public function delete()
    {
        $this->forge->deleteDomain($this->serverId, $this->siteId, $this->id);
    }
}
