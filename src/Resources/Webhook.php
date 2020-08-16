<?php

namespace Laravel\Forge\Resources;

class Webhook extends Resource
{
    /**
     * The id of the webhook.
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
     * The destination url.
     *
     * @var string
     */
    public $url;

    /**
     * The date/time the webhook was created.
     *
     * @var string
     */
    public $createdAt;

    /**
     * Delete the given webhook.
     *
     * @return void
     */
    public function delete()
    {
        $this->forge->deleteWebhook($this->serverId, $this->siteId, $this->id);
    }
}
