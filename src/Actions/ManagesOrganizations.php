<?php

declare(strict_types=1);

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Organization;
use Laravel\Forge\Resources\ServerCredential;
use Laravel\Forge\Resources\VPC;

trait ManagesOrganizations
{
    /**
     * Get the collection of organizations.
     *
     * @return Organization[]
     */
    public function organizations(): array
    {
        return $this->transformCollection(
            $this->get('orgs')['data'] ?? [],
            Organization::class
        );
    }

    /**
     * Get a specific organization.
     */
    public function organization(string $organizationSlug): Organization
    {
        return new Organization($this->get("orgs/{$organizationSlug}")['data'] ?? [], $this);
    }

    /**
     * Get the collection of server credentials for an organization.
     *
     * @return ServerCredential[]
     */
    public function serverCredentials(string $organizationSlug): array
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/server-credentials")['data'] ?? [],
            ServerCredential::class,
            $organizationSlug,
        );
    }

    /**
     * Get a specific server credential.
     */
    public function serverCredential(string $organizationSlug, int $credentialId): ServerCredential
    {
        return new ServerCredential(
            $this->get("orgs/{$organizationSlug}/server-credentials/{$credentialId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a new VPC.
     */
    public function createVpc(string $organizationSlug, int $credentialId, string $region, array $data = []): VPC
    {
        $vpc = $this->post(
            "orgs/{$organizationSlug}/server-credentials/{$credentialId}/regions/{$region}/vpcs",
            $data
        )['data'] ?? [];

        return new VPC($vpc, $this);
    }

    /**
     * Get the collection of VPCs.
     *
     * @return VPC[]
     */
    public function vpcs(string $organizationSlug, int $credentialId, string $region): array
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/server-credentials/{$credentialId}/regions/{$region}/vpcs")['data'] ?? [],
            VPC::class
        );
    }

    /**
     * Get a specific VPC.
     */
    public function vpc(string $organizationSlug, int $credentialId, string $region, int $vpcId): VPC
    {
        return new VPC(
            $this->get("orgs/{$organizationSlug}/server-credentials/{$credentialId}/regions/{$region}/vpcs/{$vpcId}")['data'] ?? [],
            $this
        );
    }
}
