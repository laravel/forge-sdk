<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Worker;

trait ManagesWorkers
{
    /**
     * Get the collection of workers.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @return \Laravel\Forge\Resources\Worker[]
     */
    public function workers($serverId, $siteId)
    {
        return $this->transformCollection(
            $this->get("servers/$serverId/sites/$siteId/workers")['workers'],
            Worker::class,
            ['server_id' => $serverId, 'site_id' => $siteId]
        );
    }

    /**
     * Get a worker instance.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @param  int  $workerId
     * @return \Laravel\Forge\Resources\Worker
     */
    public function worker($serverId, $siteId, $workerId)
    {
        return new Worker(
            $this->get("servers/$serverId/sites/$siteId/workers/$workerId")['worker']
            + ['server_id' => $serverId, 'site_id' => $siteId], $this
        );
    }

    /**
     * Create a new worker.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @param  array  $data
     * @param  bool  $wait
     * @return \Laravel\Forge\Resources\Worker
     */
    public function createWorker($serverId, $siteId, array $data, $wait = true)
    {
        $worker = $this->post("servers/$serverId/sites/$siteId/workers", $data)['worker'];

        if ($wait) {
            return $this->retry($this->getTimeout(), function () use ($serverId, $siteId, $worker) {
                $worker = $this->worker($serverId, $siteId, $worker['id']);

                return $worker->status == 'installed' ? $worker : null;
            });
        }

        return new Worker($worker + ['server_id' => $serverId, 'site_id' => $siteId], $this);
    }

    /**
     * Delete the given worker.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @param  int  $workerId
     * @return void
     */
    public function deleteWorker($serverId, $siteId, $workerId)
    {
        $this->delete("servers/$serverId/sites/$siteId/workers/$workerId");
    }

    /**
     * Restart the given worker.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @param  int  $workerId
     * @param  bool  $wait
     * @return void
     */
    public function restartWorker($serverId, $siteId, $workerId, $wait = true)
    {
        $this->post("servers/$serverId/sites/$siteId/workers/$workerId/restart");

        if ($wait) {
            $this->retry(30, function () use ($serverId, $siteId, $workerId) {
                $key = $this->worker($serverId, $siteId, $workerId);

                return $key->status == 'installed';
            });
        }
    }
}
