<?php

namespace Laravel\Forge\Resources;

class Webhook extends Resource
{
    /**
     * The id of the webhook.
     *
     * @var integer
     */
    public $id;

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
        return $this->forge->deleteWebhook($this->serverId, $this->siteId, $this->id);
    }
}
