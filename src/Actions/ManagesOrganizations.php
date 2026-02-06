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
     * @param  string  $organizationSlug
     * @return \Laravel\Forge\Resources\Organization
     */
    public function organization($organizationSlug)
    {
        return new Organization($this->get("orgs/{$organizationSlug}")['data'] ?? [], $this);
    }

    /**
     * Get the collection of server credentials for an organization.
     *
     * @param  string  $organizationSlug
     * @return \Laravel\Forge\Resources\ServerCredential[]
     */
    public function serverCredentials($organizationSlug)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/server-credentials")['data'] ?? [],
            ServerCredential::class,
            ['organization_id' => $organizationSlug]
        );
    }

    /**
     * Get a specific server credential.
     *
     * @param  string  $organizationSlug
     * @param  string  $credentialId
     * @return \Laravel\Forge\Resources\ServerCredential
     */
    public function serverCredential($organizationSlug, $credentialId)
    {
        return new ServerCredential(
            $this->get("orgs/{$organizationSlug}/server-credentials/{$credentialId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a new VPC.
     *
     * @param  string  $organizationSlug
     * @param  string  $credentialId
     * @param  string  $region
     * @return \Laravel\Forge\Resources\VPC
     */
    public function createVpc($organizationSlug, $credentialId, $region, array $data = [])
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
     * @param  string  $organizationSlug
     * @param  string  $credentialId
     * @param  string  $region
     * @return \Laravel\Forge\Resources\VPC[]
     */
    public function vpcs($organizationSlug, $credentialId, $region)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/server-credentials/{$credentialId}/regions/{$region}/vpcs")['data'] ?? [],
            VPC::class
        );
    }

    /**
     * Get a specific VPC.
     *
     * @param  string  $organizationSlug
     * @param  string  $credentialId
     * @param  string  $region
     * @param  string  $vpcId
     * @return \Laravel\Forge\Resources\VPC
     */
    public function vpc($organizationSlug, $credentialId, $region, $vpcId)
    {
        return new VPC(
            $this->get("orgs/{$organizationSlug}/server-credentials/{$credentialId}/regions/{$region}/vpcs/{$vpcId}")['data'] ?? [],
            $this
        );
    }
}
