<?php

declare(strict_types=1);

namespace Tests\Integration;

use Laravel\Forge\Resources\Domain;
use Laravel\Forge\Resources\Site;

class SitesTest extends IntegrationTestCase
{
    private function firstSite(): Site
    {
        $sites = $this->forge()->serverSites($this->organization(), $this->serverId());

        if (count($sites) === 0) {
            $this->markTestSkipped('No sites found on the test server.');
        }

        return $sites[0];
    }

    public function test_list_server_sites(): void
    {
        $sites = $this->forge()->serverSites($this->organization(), $this->serverId());

        $this->assertIsArray($sites);

        if (count($sites) === 0) {
            $this->markTestSkipped('No sites found on the test server.');
        }

        $site = $sites[0];
        $this->assertInstanceOf(Site::class, $site);
        $this->assertIsInt($site->id);
        $this->assertIsString($site->name);
        $this->assertNotEmpty($site->name);
        $this->assertIsString($site->status);
    }

    public function test_site_core_properties(): void
    {
        $site = $this->firstSite();

        $this->assertIsInt($site->id);
        $this->assertIsString($site->name);
        $this->assertIsString($site->status);
        $this->assertIsString($site->createdAt, 'createdAt should be hydrated');
        $this->assertIsString($site->updatedAt, 'updatedAt should be hydrated');
    }

    public function test_site_v2_properties(): void
    {
        $site = $this->firstSite();

        // These should always be present on an installed site
        $this->assertIsString($site->user, 'user should be hydrated');
        $this->assertNotEmpty($site->user);

        // Boolean properties
        $this->assertIsBool($site->isolated, 'isolated should be hydrated');
        $this->assertIsBool($site->wildcards, 'wildcards should be hydrated');

        // Array properties
        $this->assertIsArray($site->aliases, 'aliases should be an array');
        $this->assertIsArray($site->sharedPaths, 'sharedPaths should be an array');

        // Optional but typed
        $this->assertTrue(
            is_null($site->url) || is_string($site->url),
            'url should be null or string'
        );
        $this->assertTrue(
            is_null($site->phpVersion) || is_string($site->phpVersion),
            'phpVersion should be null or string'
        );
        $this->assertTrue(
            is_null($site->https) || is_bool($site->https),
            'https should be null or bool'
        );
        $this->assertTrue(
            is_null($site->webDirectory) || is_string($site->webDirectory),
            'webDirectory should be null or string'
        );
        $this->assertTrue(
            is_null($site->rootDirectory) || is_string($site->rootDirectory),
            'rootDirectory should be null or string'
        );
        $this->assertTrue(
            is_null($site->appType) || is_string($site->appType),
            'appType should be null or string'
        );
        $this->assertTrue(
            is_null($site->zeroDowntimeDeployments) || is_bool($site->zeroDowntimeDeployments),
            'zeroDowntimeDeployments should be null or bool'
        );
        $this->assertTrue(
            is_null($site->usesEnvoyer) || is_bool($site->usesEnvoyer),
            'usesEnvoyer should be null or bool'
        );
        $this->assertTrue(
            is_null($site->maintenanceMode) || is_array($site->maintenanceMode),
            'maintenanceMode should be null or array'
        );
    }

    public function test_site_repository_is_mixed(): void
    {
        $site = $this->firstSite();

        // v2 returns repository as a nested object {provider, url, branch, status}
        // or null if no repo installed — both are valid
        $this->assertTrue(
            is_null($site->repository) || is_array($site->repository) || is_string($site->repository),
            'repository should be null, array (v2), or string (v1)'
        );
    }

    public function test_site_has_no_jsonapi_envelope_keys(): void
    {
        $site = $this->firstSite();

        $this->assertArrayNotHasKey('relationships', $site->attributes);
        $this->assertArrayNotHasKey('links', $site->attributes);
    }

    public function test_crud_site_domain(): void
    {
        $org = $this->organization();
        $serverId = $this->serverId();
        $site = $this->firstSite();
        $suffix = time();
        $domainName = "sdk-test-{$suffix}.example.com";

        // Create
        $domain = $this->forge()->createDomain($org, $serverId, $site->id, [
            'name' => $domainName,
            'allow_wildcard_subdomains' => false,
            'www_redirect_type' => 'none',
        ]);

        $this->assertInstanceOf(Domain::class, $domain);
        $this->assertIsInt($domain->id);

        try {
            usleep(500_000);

            // List
            $domains = $this->forge()->domains($org, $serverId, $site->id);
            $this->assertIsArray($domains);
            $this->assertNotEmpty($domains);

            $found = array_filter($domains, fn (Domain $d) => $d->id === $domain->id);
            $this->assertNotEmpty($found, 'Created domain should appear in listing');

            usleep(500_000);

            // Read
            $fetched = $this->forge()->domain($org, $serverId, $site->id, $domain->id);
            $this->assertInstanceOf(Domain::class, $fetched);
            $this->assertSame($domain->id, $fetched->id);
            $this->assertIsString($fetched->name);
            $this->assertNotEmpty($fetched->name);
            $this->assertIsString($fetched->status);
            $this->assertTrue(
                is_null($fetched->createdAt) || is_string($fetched->createdAt),
                'createdAt should be null or string'
            );
            $this->assertTrue(
                is_null($fetched->type) || is_string($fetched->type),
                'type should be null or string'
            );
            $this->assertTrue(
                is_null($fetched->primary) || is_bool($fetched->primary),
                'primary should be null or bool'
            );

            // Envelope keys stripped
            $this->assertArrayNotHasKey('relationships', $fetched->attributes);
            $this->assertArrayNotHasKey('links', $fetched->attributes);
        } finally {
            usleep(500_000);
            $this->forge()->deleteDomain($org, $serverId, $site->id, $domain->id);
        }
    }
}
