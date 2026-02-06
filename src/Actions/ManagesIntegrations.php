<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Integration;

trait ManagesIntegrations
{
    /**
     * Get the Laravel Horizon integration status.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Integration
     */
    public function getHorizon($organizationSlug, $serverId, $siteId)
    {
        return new Integration(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/horizon")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a Laravel Horizon integration.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Integration
     */
    public function createHorizon($organizationSlug, $serverId, $siteId, array $data = [])
    {
        $integration = $this->post(
            "orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/horizon",
            $data
        )['data'] ?? [];

        return new Integration(
            $integration + [
                'organization_id' => $organizationSlug,
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
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return void
     */
    public function deleteHorizon($organizationSlug, $serverId, $siteId)
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/horizon");
    }

    /**
     * Get the Laravel Octane integration status.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Integration
     */
    public function getOctane($organizationSlug, $serverId, $siteId)
    {
        return new Integration(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/octane")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a Laravel Octane integration.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Integration
     */
    public function createOctane($organizationSlug, $serverId, $siteId, array $data = [])
    {
        $integration = $this->post(
            "orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/octane",
            $data
        )['data'] ?? [];

        return new Integration(
            $integration + [
                'organization_id' => $organizationSlug,
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
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return void
     */
    public function deleteOctane($organizationSlug, $serverId, $siteId)
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/octane");
    }

    /**
     * Get the Laravel Reverb integration status.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Integration
     */
    public function getReverb($organizationSlug, $serverId, $siteId)
    {
        return new Integration(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/reverb")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a Laravel Reverb integration.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Integration
     */
    public function createReverb($organizationSlug, $serverId, $siteId, array $data = [])
    {
        $integration = $this->post(
            "orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/reverb",
            $data
        )['data'] ?? [];

        return new Integration(
            $integration + [
                'organization_id' => $organizationSlug,
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
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return void
     */
    public function deleteReverb($organizationSlug, $serverId, $siteId)
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/reverb");
    }

    /**
     * Get the Inertia integration status.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Integration
     */
    public function getInertia($organizationSlug, $serverId, $siteId)
    {
        return new Integration(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/inertia")['data'] ?? [],
            $this
        );
    }

    /**
     * Create an Inertia integration.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Integration
     */
    public function createInertia($organizationSlug, $serverId, $siteId, array $data = [])
    {
        $integration = $this->post(
            "orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/inertia",
            $data
        )['data'] ?? [];

        return new Integration(
            $integration + [
                'organization_id' => $organizationSlug,
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
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Integration
     */
    public function getPulse($organizationSlug, $serverId, $siteId)
    {
        return new Integration(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/pulse")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a Laravel Pulse integration.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Integration
     */
    public function createPulse($organizationSlug, $serverId, $siteId, array $data = [])
    {
        $integration = $this->post(
            "orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/pulse",
            $data
        )['data'] ?? [];

        return new Integration(
            $integration + [
                'organization_id' => $organizationSlug,
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
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return void
     */
    public function deletePulse($organizationSlug, $serverId, $siteId)
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/pulse");
    }

    /**
     * Get the Laravel Maintenance integration status.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Integration
     */
    public function getMaintenance($organizationSlug, $serverId, $siteId)
    {
        return new Integration(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/laravel-maintenance")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a Laravel Maintenance integration.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Integration
     */
    public function createMaintenance($organizationSlug, $serverId, $siteId, array $data = [])
    {
        $integration = $this->post(
            "orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/laravel-maintenance",
            $data
        )['data'] ?? [];

        return new Integration(
            $integration + [
                'organization_id' => $organizationSlug,
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
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return void
     */
    public function deleteMaintenance($organizationSlug, $serverId, $siteId)
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/laravel-maintenance");
    }

    /**
     * Get the Laravel Scheduler integration job.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Integration
     */
    public function getScheduler($organizationSlug, $serverId, $siteId)
    {
        return new Integration(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/laravel-scheduler")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a Laravel Scheduler integration job.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return \Laravel\Forge\Resources\Integration
     */
    public function createScheduler($organizationSlug, $serverId, $siteId, array $data = [])
    {
        $integration = $this->post(
            "orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/laravel-scheduler",
            $data
        )['data'] ?? [];

        return new Integration(
            $integration + [
                'organization_id' => $organizationSlug,
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
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $siteId
     * @return void
     */
    public function deleteScheduler($organizationSlug, $serverId, $siteId)
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/laravel-scheduler");
    }
}
