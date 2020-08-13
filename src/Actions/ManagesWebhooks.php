<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Webhook;

trait ManagesWebhooks
{
    /**
     * Get the collection of webhooks.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @return \Laravel\Forge\Resources\Webhook[]
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
     * @param  int  $serverId
     * @param  int  $siteId
     * @param  int  $webhookId
     * @return \Laravel\Forge\Resources\Webhook
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
     * @param  int  $serverId
     * @param  int  $siteId
     * @param  array  $data
     * @return \Laravel\Forge\Resources\Webhook
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
     * @param  int  $serverId
     * @param  int  $siteId
     * @param  int  $webhookId
     * @return void
     */
    public function deleteWebhook($serverId, $siteId, $webhookId)
    {
        $this->delete("servers/$serverId/sites/$siteId/webhooks/$webhookId");
    }
}
