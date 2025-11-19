<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Organization;
use Laravel\Forge\Resources\ServerCredential;
use Laravel\Forge\Resources\VPC;

trait ManagesOrganizations
{
    /**
     * Get the collection of organizations.
     *
     * @return \Laravel\Forge\Resources\Organization[]
     */
    public function organizations()
    {
        return $this->transformCollection(
            $this->get('orgs')['data'] ?? [],
            Organization::class
        );
    }

    /**
     * Get a specific organization.
     *
     * @param  string  $organizationId
     * @return \Laravel\Forge\Resources\Organization
     */
    public function organization($organizationId)
    {
        return new Organization($this->get("orgs/{$organizationId}")['data'] ?? [], $this);
    }

    /**
     * Get the collection of server credentials for an organization.
     *
     * @param  string  $organizationId
     * @return \Laravel\Forge\Resources\ServerCredential[]
     */
    public function serverCredentials($organizationId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationId}/server-credentials")['data'] ?? [],
            ServerCredential::class,
            ['organization_id' => $organizationId]
        );
    }

    /**
     * Get a specific server credential.
     *
     * @param  string  $organizationId
     * @param  string  $credentialId
     * @return \Laravel\Forge\Resources\ServerCredential
     */
    public function serverCredential($organizationId, $credentialId)
    {
        return new ServerCredential(
            $this->get("orgs/{$organizationId}/server-credentials/{$credentialId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a new VPC.
     *
     * @param  string  $organizationId
     * @param  string  $credentialId
     * @param  string  $region
     * @param  array  $data
     * @return \Laravel\Forge\Resources\VPC
     */
    public function createVpc($organizationId, $credentialId, $region, array $data = [])
    {
        $vpc = $this->post(
            "orgs/{$organizationId}/server-credentials/{$credentialId}/regions/{$region}/vpcs",
            $data
        )['data'] ?? [];

        return new VPC($vpc, $this);
    }

    /**
     * Get the collection of VPCs.
     *
     * @param  string  $organizationId
     * @param  string  $credentialId
     * @param  string  $region
     * @return \Laravel\Forge\Resources\VPC[]
     */
    public function vpcs($organizationId, $credentialId, $region)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationId}/server-credentials/{$credentialId}/regions/{$region}/vpcs")['data'] ?? [],
            VPC::class
        );
    }

    /**
     * Get a specific VPC.
     *
     * @param  string  $organizationId
     * @param  string  $credentialId
     * @param  string  $region
     * @param  string  $vpcId
     * @return \Laravel\Forge\Resources\VPC
     */
    public function vpc($organizationId, $credentialId, $region, $vpcId)
    {
        return new VPC(
            $this->get("orgs/{$organizationId}/server-credentials/{$credentialId}/regions/{$region}/vpcs/{$vpcId}")['data'] ?? [],
            $this
        );
    }
}
