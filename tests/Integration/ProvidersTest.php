<?php

declare(strict_types=1);

namespace Tests\Integration;

use Laravel\Forge\CursorPaginator;
use Laravel\Forge\Resources\Provider;
use Laravel\Forge\Resources\ProviderRegion;
use Laravel\Forge\Resources\ProviderSize;

class ProvidersTest extends IntegrationTestCase
{
    public function test_list_providers(): void
    {
        $providers = $this->forge()->providers();

        $this->assertInstanceOf(CursorPaginator::class, $providers);
        $this->assertNotEmpty($providers);
        $this->assertContainsOnlyInstancesOf(Provider::class, $providers);

        $provider = $providers[0];
        $this->assertInstanceOf(Provider::class, $provider);
        $this->assertIsInt($provider->id);
        $this->assertIsString($provider->name);
        $this->assertNotEmpty($provider->name);

        // v2 properties
        $this->assertIsString($provider->slug, 'slug should be hydrated');
        $this->assertNotEmpty($provider->slug);

        $this->assertTrue(
            is_null($provider->simpleName) || is_string($provider->simpleName),
            'simpleName should be null or string'
        );
        $this->assertTrue(
            is_null($provider->label) || is_string($provider->label),
            'label should be null or string'
        );
        $this->assertTrue(
            is_null($provider->supportsLoadBalancers) || is_bool($provider->supportsLoadBalancers),
            'supportsLoadBalancers should be null or bool'
        );
        $this->assertTrue(
            is_null($provider->supportsVpcs) || is_bool($provider->supportsVpcs),
            'supportsVpcs should be null or bool'
        );
        $this->assertTrue(
            is_null($provider->currency) || is_string($provider->currency),
            'currency should be null or string'
        );
        $this->assertTrue(
            is_null($provider->currencySymbol) || is_string($provider->currencySymbol),
            'currencySymbol should be null or string'
        );
    }

    public function test_provider_has_no_jsonapi_envelope_keys(): void
    {
        $providers = $this->forge()->providers();
        $provider = $providers[0];

        $this->assertIsArray($provider->relationships);
        $this->assertIsArray($provider->links);
    }

    public function test_provider_sizes(): void
    {
        $providers = $this->forge()->providers();
        $this->assertNotEmpty($providers);

        $providerId = $providers[0]->id;
        $sizes = $this->forge()->providerSizes($providerId);

        $this->assertInstanceOf(CursorPaginator::class, $sizes);

        if (count($sizes) === 0) {
            $this->markTestSkipped('No sizes found for provider.');
        }

        $this->assertContainsOnlyInstancesOf(ProviderSize::class, $sizes);

        $size = $sizes[0];
        $this->assertInstanceOf(ProviderSize::class, $size);
        $this->assertIsInt($size->id);

        // v2 properties
        $this->assertTrue(
            is_null($size->code) || is_string($size->code),
            'code should be null or string'
        );
        $this->assertTrue(
            is_null($size->name) || is_string($size->name),
            'name should be null or string'
        );
        $this->assertTrue(
            is_null($size->category) || is_string($size->category),
            'category should be null or string'
        );
        $this->assertTrue(
            is_null($size->cpus) || is_int($size->cpus),
            'cpus should be null or int'
        );
        $this->assertTrue(
            is_null($size->ram) || is_int($size->ram),
            'ram should be null or int'
        );
        $this->assertTrue(
            is_null($size->diskType) || is_string($size->diskType),
            'diskType should be null or string'
        );
        $this->assertTrue(
            is_null($size->architecture) || is_string($size->architecture),
            'architecture should be null or string'
        );

        // Envelope keys stripped
        $this->assertIsArray($size->relationships);
        $this->assertIsArray($size->links);
    }

    public function test_provider_regions(): void
    {
        $providers = $this->forge()->providers();
        $this->assertNotEmpty($providers);

        $providerId = $providers[0]->id;
        $regions = $this->forge()->providerRegions($providerId);

        $this->assertInstanceOf(CursorPaginator::class, $regions);

        if (count($regions) === 0) {
            $this->markTestSkipped('No regions found for provider.');
        }

        $this->assertContainsOnlyInstancesOf(ProviderRegion::class, $regions);

        $region = $regions[0];
        $this->assertInstanceOf(ProviderRegion::class, $region);
        $this->assertIsInt($region->id);
        $this->assertIsString($region->name);

        // v2 properties
        $this->assertTrue(
            is_null($region->code) || is_string($region->code),
            'code should be null or string'
        );
        $this->assertTrue(
            is_null($region->alternateCode) || is_string($region->alternateCode),
            'alternateCode should be null or string'
        );
        $this->assertTrue(
            is_null($region->label) || is_string($region->label),
            'label should be null or string'
        );

        // Envelope keys stripped
        $this->assertIsArray($region->relationships);
        $this->assertIsArray($region->links);
    }
}
