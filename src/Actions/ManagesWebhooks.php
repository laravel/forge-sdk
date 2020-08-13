<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Webhook;

trait ManagesWebhooks
{
    /**
     * Get the collection of webhooks.
     *
     * @param  integer $serverId
     * @param  integer $siteId
     * @return Webhook[]
     */
    public function webhooks($serverId, $siteId)
    {
        return $this->transformCollection(
            $this->get("servers/$serverId/sites/$siteId/webhooks")['webhooks'],
            Webhook::class,
            ['server_id' => $serverId, 'site_id' => $siteId]
        );
    }

    /**
     * Get a webhook instance.
     *
     * @param  integer $serverId
     * @param  integer $siteId
     * @param  integer $webhookId
     * @return Webhook
     */
    public function webhook($serverId, $siteId, $webhookId)
    {
        return new Webhook(
            $this->get("servers/$serverId/sites/$siteId/webhooks/$webhookId")['webhook']
            + ['server_id' => $serverId, 'site_id' => $siteId], $this
        );
    }

    /**
     * Create a new webhook.
     *
     * @param  integer $serverId
     * @param  integer $siteId
     * @param  array $data
     * @return Webhook
     */
    public function createWebhook($serverId, $siteId, array $data)
    {
        return new Webhook(
            $this->post("servers/$serverId/sites/$siteId/webhooks", $data)['webhook']
            + ['server_id' => $serverId, 'site_id' => $siteId], $this
        );
    }

    /**
     * Delete the given webhook.
     *
     * @param  integer $serverId
     * @param  integer $siteId
     * @param  integer $webhookId
     * @return void
     */
    public function deleteWebhook($serverId, $siteId, $webhookId)
    {
        $this->delete("servers/$serverId/sites/$siteId/webhooks/$webhookId");
    }
}
