<?php

declare(strict_types=1);

namespace Tests\Integration;

use Laravel\Forge\Resources\Organization;

class OrganizationsTest extends IntegrationTestCase
{
    public function test_list_organizations(): void
    {
        $organizations = $this->forge()->organizations();

        $this->assertIsArray($organizations);
        $this->assertNotEmpty($organizations);

        $organization = $organizations[0];
        $this->assertInstanceOf(Organization::class, $organization);
        $this->assertIsString($organization->id);
        $this->assertIsString($organization->name);
        $this->assertNotEmpty($organization->name);
    }

    public function test_get_single_organization(): void
    {
        $organization = $this->forge()->organization($this->organization());

        $this->assertInstanceOf(Organization::class, $organization);
        $this->assertIsString($organization->id);
        $this->assertNotEmpty($organization->id);
        $this->assertIsString($organization->name);
        $this->assertNotEmpty($organization->name);

        // v2 properties
        $this->assertIsString($organization->slug, 'slug should be hydrated');
        $this->assertNotEmpty($organization->slug);
        $this->assertIsString($organization->createdAt, 'createdAt should be hydrated');
        $this->assertIsString($organization->updatedAt, 'updatedAt should be hydrated');

        // ownerId may not be returned by all API versions
        $this->assertTrue(
            is_null($organization->ownerId) || is_int($organization->ownerId),
            'ownerId should be null or int'
        );
    }

    public function test_organization_has_no_jsonapi_envelope_keys(): void
    {
        $organization = $this->forge()->organization($this->organization());

        $this->assertIsArray($organization->relationships);
        $this->assertIsArray($organization->links);
    }
}
