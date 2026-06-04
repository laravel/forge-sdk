<?php

declare(strict_types=1);

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Integration;

trait ManagesIntegrations
{
    /**
     * Get the Laravel Horizon integration status.
     */
    public function getHorizon(string $organizationSlug, int $serverId, int $siteId): Integration
    {
        return new Integration(
            $this->normalizeIntegration(
                $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/horizon")['data'] ?? [],
                'horizon',
                $organizationSlug,
                $serverId,
                $siteId,
            ),
            $this
        );
    }

    /**
     * Create a Laravel Horizon integration.
     */
    public function createHorizon(string $organizationSlug, int $serverId, int $siteId, array $data = []): Integration
    {
        return new Integration(
            $this->normalizeIntegration(
                $this->post(
                    "orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/horizon",
                    $data
                )['data'] ?? [],
                'horizon',
                $organizationSlug,
                $serverId,
                $siteId,
            ),
            $this
        );
    }

    /**
     * Delete the Laravel Horizon integration.
     */
    public function deleteHorizon(string $organizationSlug, int $serverId, int $siteId): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/horizon");
    }

    /**
     * Get the Laravel Octane integration status.
     */
    public function getOctane(string $organizationSlug, int $serverId, int $siteId): Integration
    {
        return new Integration(
            $this->normalizeIntegration(
                $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/octane")['data'] ?? [],
                'octane',
                $organizationSlug,
                $serverId,
                $siteId,
            ),
            $this
        );
    }

    /**
     * Create a Laravel Octane integration.
     */
    public function createOctane(string $organizationSlug, int $serverId, int $siteId, array $data = []): Integration
    {
        return new Integration(
            $this->normalizeIntegration(
                $this->post(
                    "orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/octane",
                    $data
                )['data'] ?? [],
                'octane',
                $organizationSlug,
                $serverId,
                $siteId,
            ),
            $this
        );
    }

    /**
     * Delete the Laravel Octane integration.
     */
    public function deleteOctane(string $organizationSlug, int $serverId, int $siteId): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/octane");
    }

    /**
     * Get the Laravel Reverb integration status.
     */
    public function getReverb(string $organizationSlug, int $serverId, int $siteId): Integration
    {
        return new Integration(
            $this->normalizeIntegration(
                $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/reverb")['data'] ?? [],
                'reverb',
                $organizationSlug,
                $serverId,
                $siteId,
            ),
            $this
        );
    }

    /**
     * Create a Laravel Reverb integration.
     */
    public function createReverb(string $organizationSlug, int $serverId, int $siteId, array $data = []): Integration
    {
        return new Integration(
            $this->normalizeIntegration(
                $this->post(
                    "orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/reverb",
                    $data
                )['data'] ?? [],
                'reverb',
                $organizationSlug,
                $serverId,
                $siteId,
            ),
            $this
        );
    }

    /**
     * Delete the Laravel Reverb integration.
     */
    public function deleteReverb(string $organizationSlug, int $serverId, int $siteId): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/reverb");
    }

    /**
     * Get the Inertia integration status.
     */
    public function getInertia(string $organizationSlug, int $serverId, int $siteId): Integration
    {
        return new Integration(
            $this->normalizeIntegration(
                $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/inertia")['data'] ?? [],
                'inertia',
                $organizationSlug,
                $serverId,
                $siteId,
            ),
            $this
        );
    }

    /**
     * Create an Inertia integration.
     */
    public function createInertia(string $organizationSlug, int $serverId, int $siteId, array $data = []): Integration
    {
        return new Integration(
            $this->normalizeIntegration(
                $this->post(
                    "orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/inertia",
                    $data
                )['data'] ?? [],
                'inertia',
                $organizationSlug,
                $serverId,
                $siteId,
            ),
            $this
        );
    }

    /**
     * Get the Laravel Pulse integration status.
     */
    public function getPulse(string $organizationSlug, int $serverId, int $siteId): Integration
    {
        return new Integration(
            $this->normalizeIntegration(
                $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/pulse")['data'] ?? [],
                'pulse',
                $organizationSlug,
                $serverId,
                $siteId,
            ),
            $this
        );
    }

    /**
     * Create a Laravel Pulse integration.
     */
    public function createPulse(string $organizationSlug, int $serverId, int $siteId, array $data = []): Integration
    {
        return new Integration(
            $this->normalizeIntegration(
                $this->post(
                    "orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/pulse",
                    $data
                )['data'] ?? [],
                'pulse',
                $organizationSlug,
                $serverId,
                $siteId,
            ),
            $this
        );
    }

    /**
     * Delete the Laravel Pulse integration.
     */
    public function deletePulse(string $organizationSlug, int $serverId, int $siteId): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/pulse");
    }

    /**
     * Get the Laravel Maintenance integration status.
     */
    public function getMaintenance(string $organizationSlug, int $serverId, int $siteId): Integration
    {
        return new Integration(
            $this->normalizeIntegration(
                $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/laravel-maintenance")['data'] ?? [],
                'laravel-maintenance',
                $organizationSlug,
                $serverId,
                $siteId,
            ),
            $this
        );
    }

    /**
     * Create a Laravel Maintenance integration.
     */
    public function createMaintenance(string $organizationSlug, int $serverId, int $siteId, array $data = []): void
    {
        $this->post("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/laravel-maintenance", $data);
    }

    /**
     * Delete the Laravel Maintenance integration.
     */
    public function deleteMaintenance(string $organizationSlug, int $serverId, int $siteId): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/laravel-maintenance");
    }

    /**
     * Get the Laravel Scheduler integration job.
     */
    public function getScheduler(string $organizationSlug, int $serverId, int $siteId): Integration
    {
        return new Integration(
            $this->normalizeIntegration(
                $this->get("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/laravel-scheduler")['data'] ?? [],
                'laravel-scheduler',
                $organizationSlug,
                $serverId,
                $siteId,
            ),
            $this
        );
    }

    /**
     * Create a Laravel Scheduler integration job.
     */
    public function createScheduler(string $organizationSlug, int $serverId, int $siteId, array $data = []): Integration
    {
        return new Integration(
            $this->normalizeIntegration(
                $this->post(
                    "orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/laravel-scheduler",
                    $data
                )['data'] ?? [],
                'laravel-scheduler',
                $organizationSlug,
                $serverId,
                $siteId,
            ),
            $this
        );
    }

    /**
     * Delete the Laravel Scheduler integration job.
     */
    public function deleteScheduler(string $organizationSlug, int $serverId, int $siteId): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/sites/{$siteId}/integrations/laravel-scheduler");
    }

    /**
     * Normalize integration API response data.
     *
     * Each integration type returns a type-specific "installed" key
     * (e.g. "horizon_installed", "octane_installed"). This method
     * normalizes it to the generic "installed" key and injects
     * context fields so the Integration resource is fully hydrated.
     */
    private function normalizeIntegration(
        array $data,
        string $type,
        string $organizationSlug,
        int $serverId,
        int $siteId,
    ): array {
        $installedKeys = [
            'horizon' => 'horizon_installed',
            'octane' => 'octane_installed',
            'reverb' => 'reverb_installed',
            'inertia' => 'inertia_installed',
            'pulse' => 'pulse_installed',
            'laravel-maintenance' => 'laravel_installed',
            'laravel-scheduler' => 'laravel_installed',
        ];

        $key = $installedKeys[$type] ?? null;

        if ($key !== null) {
            // Handle JSON:API wrapped response.
            if (isset($data['attributes']) && is_array($data['attributes']) && array_key_exists($key, $data['attributes'])) {
                $data['attributes']['installed'] = $data['attributes'][$key];
                unset($data['attributes'][$key]);
            }
            // Handle flat response.
            elseif (array_key_exists($key, $data)) {
                $data['installed'] = $data[$key];
                unset($data[$key]);
            }
        }

        // Inject type into the attributes hash (if present) so it survives
        // fill()'s envelope-type stripping. For flat responses, set it directly.
        if (isset($data['attributes']) && is_array($data['attributes'])) {
            $data['attributes']['type'] = $type;
        }

        $data['type'] = $type;

        return $data + [
            'organization_slug' => $organizationSlug,
            'server_id' => $serverId,
            'site_id' => $siteId,
        ];
    }
}
