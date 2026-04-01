<?php

declare(strict_types=1);

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Laravel\Forge\Forge;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * Tests that verify the SDK correctly handles JSON:API formatted responses
 * where attributes are nested under an "attributes" key and envelope keys
 * (type, relationships, links) are stripped.
 */
class JsonApiResponseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function test_server_from_jsonapi_response(): void
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $jsonApiPayload = json_encode([
            'data' => [
                'id' => 42,
                'type' => 'servers',
                'attributes' => [
                    'name' => 'production-web',
                    'ip_address' => '192.168.1.100',
                    'private_ip_address' => '10.0.0.5',
                    'php_version' => 'php83',
                    'ubuntu_version' => '24.04',
                    'ssh_port' => 22,
                    'provider' => 'ocean2',
                    'is_ready' => true,
                    'database_type' => 'mysql8',
                    'db_status' => 'installed',
                    'redis_status' => 'installed',
                    'opcache_status' => 'enabled',
                    'connection_status' => 'connected',
                    'timezone' => 'UTC',
                    'size' => 's-2vcpu-4gb',
                    'region' => 'nyc3',
                    'created_at' => '2025-01-01T00:00:00.000000Z',
                    'updated_at' => '2025-06-15T12:00:00.000000Z',
                ],
                'relationships' => [
                    'tags' => ['data' => []],
                ],
                'links' => [
                    'self' => 'https://forge.laravel.com/api/orgs/my-org/servers/42',
                ],
            ],
        ]);

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/42', [])->andReturn(
            new Response(200, [], $jsonApiPayload)
        );

        $server = $forge->server('org-123', 42);

        $this->assertSame(42, $server->id);
        $this->assertSame('production-web', $server->name);
        $this->assertSame('192.168.1.100', $server->ipAddress);
        $this->assertSame('10.0.0.5', $server->privateIpAddress);
        $this->assertSame('php83', $server->phpVersion);
        $this->assertSame('24.04', $server->ubuntuVersion);
        $this->assertSame(22, $server->sshPort);
        $this->assertSame('ocean2', $server->provider);
        $this->assertTrue($server->isReady);
        $this->assertSame('mysql8', $server->databaseType);
        $this->assertSame('installed', $server->dbStatus);
        $this->assertSame('installed', $server->redisStatus);
        $this->assertSame('enabled', $server->opcacheStatus);
        $this->assertSame('connected', $server->connectionStatus);
        $this->assertSame('UTC', $server->timezone);
        $this->assertSame('s-2vcpu-4gb', $server->size);
        $this->assertSame('nyc3', $server->region);
        $this->assertSame('2025-01-01T00:00:00.000000Z', $server->createdAt);
        $this->assertSame('2025-06-15T12:00:00.000000Z', $server->updatedAt);

        // Ensure envelope keys are stripped and don't pollute the object
        $this->assertFalse(property_exists($server, 'type') && $server->type === 'servers');
        $this->assertArrayNotHasKey('relationships', $server->attributes);
        $this->assertArrayNotHasKey('links', $server->attributes);
        $this->assertArrayNotHasKey('type', $server->attributes);
    }

    public function test_server_from_flat_response(): void
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $flatPayload = json_encode([
            'data' => [
                'id' => 42,
                'name' => 'production-web',
                'ip_address' => '192.168.1.100',
                'php_version' => 'php83',
                'is_ready' => true,
                'created_at' => '2025-01-01T00:00:00.000000Z',
            ],
        ]);

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/42', [])->andReturn(
            new Response(200, [], $flatPayload)
        );

        $server = $forge->server('org-123', 42);

        $this->assertSame(42, $server->id);
        $this->assertSame('production-web', $server->name);
        $this->assertSame('192.168.1.100', $server->ipAddress);
        $this->assertSame('php83', $server->phpVersion);
        $this->assertTrue($server->isReady);
    }

    public function test_site_from_jsonapi_response(): void
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $jsonApiPayload = json_encode([
            'data' => [
                'id' => 7,
                'type' => 'sites',
                'attributes' => [
                    'server_id' => 42,
                    'name' => 'example.com',
                    'url' => 'https://example.com',
                    'user' => 'forge',
                    'web_directory' => '/public',
                    'root_directory' => '/home/forge/example.com',
                    'status' => 'installed',
                    'php_version' => 'php83',
                    'https' => true,
                    'isolated' => false,
                    'shared_paths' => [],
                    'wildcards' => false,
                    'repository' => [
                        'provider' => 'github',
                        'url' => 'user/repo',
                        'branch' => 'main',
                        'status' => 'installed',
                    ],
                    'quick_deploy' => true,
                    'deployment_url' => 'https://forge.laravel.com/deploy/abc123',
                    'app_type' => 'laravel',
                    'zero_downtime_deployments' => false,
                    'uses_envoyer' => false,
                    'maintenance_mode' => ['enabled' => false, 'status' => null],
                    'created_at' => '2025-03-01T00:00:00.000000Z',
                    'updated_at' => '2025-06-15T12:00:00.000000Z',
                ],
                'relationships' => [
                    'tags' => ['data' => []],
                ],
                'links' => [
                    'self' => 'https://forge.laravel.com/api/orgs/my-org/servers/42/sites/7',
                ],
            ],
        ]);

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/42/sites', [])->andReturn(
            new Response(200, [], '{"data": ['.json_encode(json_decode($jsonApiPayload, true)['data']).']}')
        );

        $sites = $forge->serverSites('org-123', 42);
        $this->assertCount(1, $sites);

        $site = $sites[0];
        $this->assertSame(7, $site->id);
        $this->assertSame('example.com', $site->name);
        $this->assertSame('https://example.com', $site->url);
        $this->assertSame('forge', $site->user);
        $this->assertSame('/public', $site->webDirectory);
        $this->assertSame('/home/forge/example.com', $site->rootDirectory);
        $this->assertSame('installed', $site->status);
        $this->assertSame('php83', $site->phpVersion);
        $this->assertTrue($site->https);
        $this->assertFalse($site->isolated);
        $this->assertIsArray($site->sharedPaths);
        $this->assertFalse($site->wildcards);
        $this->assertTrue($site->quickDeploy);
        $this->assertSame('https://forge.laravel.com/deploy/abc123', $site->deploymentUrl);
        $this->assertSame('laravel', $site->appType);
        $this->assertFalse($site->zeroDowntimeDeployments);
        $this->assertFalse($site->usesEnvoyer);
        $this->assertIsArray($site->maintenanceMode);
        $this->assertSame('2025-03-01T00:00:00.000000Z', $site->createdAt);
        $this->assertSame('2025-06-15T12:00:00.000000Z', $site->updatedAt);

        // Repository should be the nested object (mixed type)
        $this->assertIsArray($site->repository);
        $this->assertSame('github', $site->repository['provider']);

        // Envelope keys stripped
        $this->assertArrayNotHasKey('relationships', $site->attributes);
        $this->assertArrayNotHasKey('links', $site->attributes);
        $this->assertArrayNotHasKey('type', $site->attributes);
    }

    public function test_organization_from_jsonapi_response(): void
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $jsonApiPayload = json_encode([
            'data' => [
                'id' => 'org-uuid-123',
                'type' => 'organizations',
                'attributes' => [
                    'name' => 'My Organization',
                    'slug' => 'my-org',
                    'owner_id' => 1,
                    'created_at' => '2025-01-01T00:00:00.000000Z',
                    'updated_at' => '2025-06-15T12:00:00.000000Z',
                ],
                'relationships' => [],
                'links' => [
                    'self' => 'https://forge.laravel.com/api/orgs/my-org',
                ],
            ],
        ]);

        $http->shouldReceive('request')->once()->with('GET', 'orgs/my-org', [])->andReturn(
            new Response(200, [], $jsonApiPayload)
        );

        $org = $forge->organization('my-org');

        $this->assertSame('org-uuid-123', $org->id);
        $this->assertSame('My Organization', $org->name);
        $this->assertSame('my-org', $org->slug);
        $this->assertSame(1, $org->ownerId);
        $this->assertSame('2025-01-01T00:00:00.000000Z', $org->createdAt);
        $this->assertSame('2025-06-15T12:00:00.000000Z', $org->updatedAt);

        $this->assertArrayNotHasKey('relationships', $org->attributes);
        $this->assertArrayNotHasKey('links', $org->attributes);
        $this->assertArrayNotHasKey('type', $org->attributes);
    }

    public function test_server_collection_from_jsonapi_response(): void
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $jsonApiPayload = json_encode([
            'data' => [
                [
                    'id' => 1,
                    'type' => 'servers',
                    'attributes' => [
                        'name' => 'Server One',
                        'ip_address' => '1.2.3.4',
                        'is_ready' => true,
                    ],
                    'relationships' => ['tags' => ['data' => []]],
                    'links' => ['self' => 'https://example.com/servers/1'],
                ],
                [
                    'id' => 2,
                    'type' => 'servers',
                    'attributes' => [
                        'name' => 'Server Two',
                        'ip_address' => '5.6.7.8',
                        'is_ready' => false,
                    ],
                    'relationships' => ['tags' => ['data' => []]],
                    'links' => ['self' => 'https://example.com/servers/2'],
                ],
            ],
        ]);

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers', [])->andReturn(
            new Response(200, [], $jsonApiPayload)
        );

        $servers = $forge->servers('org-123');

        $this->assertCount(2, $servers);

        $this->assertSame(1, $servers[0]->id);
        $this->assertSame('Server One', $servers[0]->name);
        $this->assertSame('1.2.3.4', $servers[0]->ipAddress);
        $this->assertTrue($servers[0]->isReady);

        $this->assertSame(2, $servers[1]->id);
        $this->assertSame('Server Two', $servers[1]->name);
        $this->assertFalse($servers[1]->isReady);

        // Envelope keys stripped from both
        foreach ($servers as $server) {
            $this->assertArrayNotHasKey('relationships', $server->attributes);
            $this->assertArrayNotHasKey('links', $server->attributes);
            $this->assertArrayNotHasKey('type', $server->attributes);
        }
    }
}
