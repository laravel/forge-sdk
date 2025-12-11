<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Integration;

trait ManagesIntegrations
{
    /**
     * Get the Laravel Horizon integration status.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Integration
     */
    public function getHorizon($organizationId, $serverId, $siteId)
    {
        return new Integration(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/integrations/horizon")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a Laravel Horizon integration.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Integration
     */
    public function createHorizon($organizationId, $serverId, $siteId, array $data = [])
    {
        $integration = $this->post(
            "orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/integrations/horizon",
            $data
        )['data'] ?? [];

        return new Integration(
            $integration + [
                'organization_id' => $organizationId,
                'server_id' => $serverId,
                'site_id' => $siteId,
                'type' => 'horizon',
            ],
            $this
        );
    }

    /**
     * Delete the Laravel Horizon integration.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return void
     */
    public function deleteHorizon($organizationId, $serverId, $siteId)
    {
        $this->delete("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/integrations/horizon");
    }

    /**
     * Get the Laravel Octane integration status.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Integration
     */
    public function getOctane($organizationId, $serverId, $siteId)
    {
        return new Integration(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/integrations/octane")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a Laravel Octane integration.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Integration
     */
    public function createOctane($organizationId, $serverId, $siteId, array $data = [])
    {
        $integration = $this->post(
            "orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/integrations/octane",
            $data
        )['data'] ?? [];

        return new Integration(
            $integration + [
                'organization_id' => $organizationId,
                'server_id' => $serverId,
                'site_id' => $siteId,
                'type' => 'octane',
            ],
            $this
        );
    }

    /**
     * Delete the Laravel Octane integration.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return void
     */
    public function deleteOctane($organizationId, $serverId, $siteId)
    {
        $this->delete("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/integrations/octane");
    }

    /**
     * Get the Laravel Reverb integration status.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Integration
     */
    public function getReverb($organizationId, $serverId, $siteId)
    {
        return new Integration(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/integrations/reverb")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a Laravel Reverb integration.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Integration
     */
    public function createReverb($organizationId, $serverId, $siteId, array $data = [])
    {
        $integration = $this->post(
            "orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/integrations/reverb",
            $data
        )['data'] ?? [];

        return new Integration(
            $integration + [
                'organization_id' => $organizationId,
                'server_id' => $serverId,
                'site_id' => $siteId,
                'type' => 'reverb',
            ],
            $this
        );
    }

    /**
     * Delete the Laravel Reverb integration.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return void
     */
    public function deleteReverb($organizationId, $serverId, $siteId)
    {
        $this->delete("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/integrations/reverb");
    }

    /**
     * Get the Inertia integration status.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Integration
     */
    public function getInertia($organizationId, $serverId, $siteId)
    {
        return new Integration(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/integrations/inertia")['data'] ?? [],
            $this
        );
    }

    /**
     * Create an Inertia integration.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Integration
     */
    public function createInertia($organizationId, $serverId, $siteId, array $data = [])
    {
        $integration = $this->post(
            "orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/integrations/inertia",
            $data
        )['data'] ?? [];

        return new Integration(
            $integration + [
                'organization_id' => $organizationId,
                'server_id' => $serverId,
                'site_id' => $siteId,
                'type' => 'inertia',
            ],
            $this
        );
    }

    /**
     * Get the Laravel Pulse integration status.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Integration
     */
    public function getPulse($organizationId, $serverId, $siteId)
    {
        return new Integration(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/integrations/pulse")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a Laravel Pulse integration.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Integration
     */
    public function createPulse($organizationId, $serverId, $siteId, array $data = [])
    {
        $integration = $this->post(
            "orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/integrations/pulse",
            $data
        )['data'] ?? [];

        return new Integration(
            $integration + [
                'organization_id' => $organizationId,
                'server_id' => $serverId,
                'site_id' => $siteId,
                'type' => 'pulse',
            ],
            $this
        );
    }

    /**
     * Delete the Laravel Pulse integration.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return void
     */
    public function deletePulse($organizationId, $serverId, $siteId)
    {
        $this->delete("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/integrations/pulse");
    }

    /**
     * Get the Laravel Maintenance integration status.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Integration
     */
    public function getMaintenance($organizationId, $serverId, $siteId)
    {
        return new Integration(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/integrations/laravel-maintenance")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a Laravel Maintenance integration.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Integration
     */
    public function createMaintenance($organizationId, $serverId, $siteId, array $data = [])
    {
        $integration = $this->post(
            "orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/integrations/laravel-maintenance",
            $data
        )['data'] ?? [];

        return new Integration(
            $integration + [
                'organization_id' => $organizationId,
                'server_id' => $serverId,
                'site_id' => $siteId,
                'type' => 'laravel-maintenance',
            ],
            $this
        );
    }

    /**
     * Delete the Laravel Maintenance integration.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return void
     */
    public function deleteMaintenance($organizationId, $serverId, $siteId)
    {
        $this->delete("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/integrations/laravel-maintenance");
    }

    /**
     * Get the Laravel Scheduler integration job.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Integration
     */
    public function getScheduler($organizationId, $serverId, $siteId)
    {
        return new Integration(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/integrations/laravel-scheduler")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a Laravel Scheduler integration job.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Integration
     */
    public function createScheduler($organizationId, $serverId, $siteId, array $data = [])
    {
        $integration = $this->post(
            "orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/integrations/laravel-scheduler",
            $data
        )['data'] ?? [];

        return new Integration(
            $integration + [
                'organization_id' => $organizationId,
                'server_id' => $serverId,
                'site_id' => $siteId,
                'type' => 'laravel-scheduler',
            ],
            $this
        );
    }

    /**
     * Delete the Laravel Scheduler integration job.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $siteId
     * @return void
     */
    public function deleteScheduler($organizationId, $serverId, $siteId)
    {
        $this->delete("orgs/{$organizationId}/servers/{$serverId}/sites/{$siteId}/integrations/laravel-scheduler");
    }
}
