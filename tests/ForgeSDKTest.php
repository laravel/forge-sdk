<?php

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Laravel\Forge\Exceptions\FailedActionException;
use Laravel\Forge\Exceptions\ForbiddenException;
use Laravel\Forge\Exceptions\NotFoundException;
use Laravel\Forge\Exceptions\RateLimitExceededException;
use Laravel\Forge\Exceptions\TimeoutException;
use Laravel\Forge\Exceptions\ValidationException;
use Laravel\Forge\Forge;
use Laravel\Forge\MakesHttpRequests;
use Mockery;
use PHPUnit\Framework\TestCase;

class ForgeSDKTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function test_getting_organizations()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "org-123", "name": "My Organization"}]}')
        );

        $this->assertCount(1, $forge->organizations());
    }

    public function test_getting_single_organization()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123', [])->andReturn(
            new Response(200, [], '{"data": {"id": "org-123", "name": "My Organization"}}')
        );

        $org = $forge->organization('org-123');
        $this->assertSame('org-123', $org->id);
    }

    public function test_getting_recipes_for_organization()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/recipes', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "1", "name": "Recipe 1"}]}')
        );

        $this->assertCount(1, $forge->recipes('org-123'));
    }

    public function test_getting_servers_for_organization()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "server-1", "name": "Server 1"}]}')
        );

        $this->assertCount(1, $forge->servers('org-123'));
    }

    public function test_getting_single_server()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1', [])->andReturn(
            new Response(200, [], '{"data": {"id": "server-1", "name": "Production Server"}}')
        );

        $server = $forge->server('org-123', 'server-1');
        $this->assertSame('server-1', $server->id);
    }

    public function test_creating_server()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers', [
            'form_params' => ['provider' => 'ocean2', 'size' => '1gb'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "server-1", "provider": "ocean2", "isReady": false}}')
        );

        $server = $forge->createServer('org-123', ['provider' => 'ocean2', 'size' => '1gb'], false);
        $this->assertSame('server-1', $server->id);
    }

    public function test_deleting_server()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/server-1', [])->andReturn(
            new Response(204)
        );

        $result = $forge->deleteServer('org-123', 'server-1');
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_sites_for_server()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/sites', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "site-1", "name": "example.com"}]}')
        );

        $this->assertCount(1, $forge->serverSites('org-123', 'server-1'));
    }

    public function test_creating_site()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/server-1/sites', [
            'form_params' => ['domain' => 'example.com'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "site-1", "domain": "example.com"}}')
        );

        $site = $forge->createSite('org-123', 'server-1', ['domain' => 'example.com']);
        $this->assertSame('site-1', $site->id);
    }

    public function test_updating_site()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/server-1/sites/site-1', [
            'form_params' => ['aliases' => ['foo.com']],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "site-1", "aliases": ["foo.com"]}}')
        );

        $site = $forge->updateSite('org-123', 'server-1', 'site-1', ['aliases' => ['foo.com']]);
        $this->assertSame(['foo.com'], $site->aliases);
    }

    public function test_deleting_site()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/server-1/sites/site-1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteSite('org-123', 'server-1', 'site-1');
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_databases()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/database/schemas', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "db-1", "name": "my_database"}]}')
        );

        $this->assertCount(1, $forge->databases('org-123', 'server-1'));
    }

    public function test_creating_database()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/server-1/database/schemas', [
            'form_params' => ['name' => 'my_database'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "db-1", "name": "my_database", "status": "installing"}}')
        );

        $database = $forge->createDatabase('org-123', 'server-1', ['name' => 'my_database'], false);
        $this->assertSame('db-1', $database->id);
    }

    public function test_deleting_database()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/server-1/database/schemas/db-1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteDatabase('org-123', 'server-1', 'db-1');
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_database_users()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/database/users', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "user-1", "name": "db_user"}]}')
        );

        $this->assertCount(1, $forge->databaseUsers('org-123', 'server-1'));
    }

    public function test_creating_database_user()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/server-1/database/users', [
            'form_params' => ['name' => 'db_user', 'password' => 'secret'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "user-1", "name": "db_user", "status": "installing"}}')
        );

        $user = $forge->createDatabaseUser('org-123', 'server-1', ['name' => 'db_user', 'password' => 'secret'], false);
        $this->assertSame('user-1', $user->id);
    }

    public function test_updating_database_user()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/server-1/database/users/user-1', [
            'form_params' => ['databases' => ['db-1']],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "user-1", "databases": ["db-1"]}}')
        );

        $user = $forge->updateDatabaseUser('org-123', 'server-1', 'user-1', ['databases' => ['db-1']]);
        $this->assertSame(['db-1'], $user->databases);
    }

    public function test_getting_background_processes()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/background-processes', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "process-1", "name": "My Process"}]}')
        );

        $this->assertCount(1, $forge->backgroundProcesses('org-123', 'server-1'));
    }

    public function test_creating_background_process()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/server-1/background-processes', [
            'form_params' => ['command' => 'node server.js'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "process-1", "command": "node server.js"}}')
        );

        $process = $forge->createBackgroundProcess('org-123', 'server-1', ['command' => 'node server.js']);
        $this->assertSame('process-1', $process->id);
    }

    public function test_updating_background_process()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/server-1/background-processes/process-1', [
            'form_params' => ['processes' => 2],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "process-1", "processes": 2}}')
        );

        $process = $forge->updateBackgroundProcess('org-123', 'server-1', 'process-1', ['processes' => 2]);
        $this->assertSame(2, $process->processes);
    }

    public function test_deleting_background_process()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/server-1/background-processes/process-1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteBackgroundProcess('org-123', 'server-1', 'process-1');
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_teams()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/teams', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "team-1", "name": "Development Team"}]}')
        );

        $this->assertCount(1, $forge->teams('org-123'));
    }

    public function test_getting_single_team()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/teams/team-1', [])->andReturn(
            new Response(200, [], '{"data": {"id": "team-1", "name": "Development Team"}}')
        );

        $team = $forge->team('org-123', 'team-1');
        $this->assertSame('team-1', $team->id);
    }

    public function test_creating_team()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/teams', [
            'form_params' => ['name' => 'New Team'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "team-2", "name": "New Team"}}')
        );

        $team = $forge->createTeam('org-123', ['name' => 'New Team']);
        $this->assertSame('team-2', $team->id);
    }

    public function test_updating_team()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/teams/team-1', [
            'form_params' => ['name' => 'Updated Team'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "team-1", "name": "Updated Team"}}')
        );

        $team = $forge->updateTeam('org-123', 'team-1', ['name' => 'Updated Team']);
        $this->assertSame('Updated Team', $team->name);
    }

    public function test_deleting_team()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/teams/team-1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteTeam('org-123', 'team-1');
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_team_members()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/teams/team-1/members', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "user-1", "name": "John Doe"}]}')
        );

        $this->assertCount(1, $forge->teamMembers('org-123', 'team-1'));
    }

    public function test_getting_roles()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/roles', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "role-1", "name": "Admin"}]}')
        );

        $this->assertCount(1, $forge->roles('org-123'));
    }

    public function test_getting_single_role()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/roles/role-1', [])->andReturn(
            new Response(200, [], '{"data": {"id": "role-1", "name": "Admin"}}')
        );

        $role = $forge->role('org-123', 'role-1');
        $this->assertSame('role-1', $role->id);
    }

    public function test_creating_role()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/roles', [
            'form_params' => ['name' => 'Developer'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "role-2", "name": "Developer"}}')
        );

        $role = $forge->createRole('org-123', ['name' => 'Developer']);
        $this->assertSame('role-2', $role->id);
    }

    public function test_updating_role()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/roles/role-1', [
            'form_params' => ['name' => 'Super Admin'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "role-1", "name": "Super Admin"}}')
        );

        $role = $forge->updateRole('org-123', 'role-1', ['name' => 'Super Admin']);
        $this->assertSame('Super Admin', $role->name);
    }

    public function test_deleting_role()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/roles/role-1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteRole('org-123', 'role-1');
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_predefined_roles()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'predefined-roles', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "admin", "name": "Administrator"}]}')
        );

        $this->assertCount(1, $forge->predefinedRoles());
    }

    public function test_getting_permissions()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'permissions', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "perm-1", "name": "manage_servers"}]}')
        );

        $this->assertCount(1, $forge->permissions());
    }

    public function test_getting_providers()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'providers', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "ocean2", "name": "Digital Ocean"}]}')
        );

        $this->assertCount(1, $forge->providers());
    }

    public function test_getting_single_provider()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'providers/ocean2', [])->andReturn(
            new Response(200, [], '{"data": {"id": "ocean2", "name": "Digital Ocean"}}')
        );

        $provider = $forge->provider('ocean2');
        $this->assertSame('ocean2', $provider->id);
    }

    public function test_getting_provider_regions()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'providers/ocean2/regions', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "nyc3", "name": "New York 3"}]}')
        );

        $this->assertCount(1, $forge->providerRegions('ocean2'));
    }

    public function test_getting_provider_sizes()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'providers/ocean2/sizes', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "1gb", "name": "1GB"}]}')
        );

        $this->assertCount(1, $forge->providerSizes('ocean2'));
    }

    public function test_getting_horizon_integration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/sites/site-1/integrations/horizon', [])->andReturn(
            new Response(200, [], '{"data": {"id": "int-1", "status": "installed"}}')
        );

        $integration = $forge->getHorizon('org-123', 'server-1', 'site-1');
        $this->assertSame('int-1', $integration->id);
    }

    public function test_creating_horizon_integration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/server-1/sites/site-1/integrations/horizon', [])->andReturn(
            new Response(200, [], '{"data": {"id": "int-1", "type": "horizon"}}')
        );

        $integration = $forge->createHorizon('org-123', 'server-1', 'site-1');
        $this->assertSame('int-1', $integration->id);
    }

    public function test_deleting_horizon_integration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/server-1/sites/site-1/integrations/horizon', [])->andReturn(
            new Response(204)
        );

        $forge->deleteHorizon('org-123', 'server-1', 'site-1');
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_creating_octane_integration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/server-1/sites/site-1/integrations/octane', [
            'form_params' => ['server' => 'roadrunner'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "int-2", "type": "octane"}}')
        );

        $integration = $forge->createOctane('org-123', 'server-1', 'site-1', ['server' => 'roadrunner']);
        $this->assertSame('int-2', $integration->id);
    }

    public function test_updating_domain_with_patch()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PATCH', 'orgs/org-123/servers/server-1/sites/site-1/domains/domain-1', [
            'form_params' => ['primary' => true],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "domain-1", "primary": true}}')
        );

        $domain = $forge->updateDomain('org-123', 'server-1', 'site-1', 'domain-1', ['primary' => true]);
        $this->assertTrue($domain->primary);
    }

    public function test_getting_domains()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/sites/site-1/domains', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "domain-1", "name": "example.com"}]}')
        );

        $this->assertCount(1, $forge->domains('org-123', 'server-1', 'site-1'));
    }

    public function test_creating_domain()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/server-1/sites/site-1/domains', [
            'form_params' => ['name' => 'api.example.com'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "domain-2", "name": "api.example.com"}}')
        );

        $domain = $forge->createDomain('org-123', 'server-1', 'site-1', ['name' => 'api.example.com']);
        $this->assertSame('domain-2', $domain->id);
    }

    public function test_deleting_domain()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/server-1/sites/site-1/domains/domain-1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteDomain('org-123', 'server-1', 'site-1', 'domain-1');
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_handling_validation_errors()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/recipes', [])->andReturn(
            new Response(422, [], '{"name": ["The name is required."]}')
        );

        try {
            $forge->recipes('org-123');
        } catch (ValidationException $e) {
        }

        $this->assertEquals(['name' => ['The name is required.']], $e->errors());
    }

    public function test_handling_404_errors()
    {
        $this->expectException(NotFoundException::class);

        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/invalid-server', [])->andReturn(
            new Response(404)
        );

        $forge->server('org-123', 'invalid-server');
    }

    public function test_handling_forbidden_requests()
    {
        $this->expectException(ForbiddenException::class);

        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers', [])->andReturn(
            new Response(403)
        );

        $forge->servers('org-123');
    }

    public function test_handling_failed_action_errors()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers', [
            'form_params' => ['invalid' => 'data'],
        ])->andReturn(
            new Response(400, [], 'Invalid server configuration!')
        );

        try {
            $forge->createServer('org-123', ['invalid' => 'data'], false);
        } catch (FailedActionException $e) {
            $this->assertSame('Invalid server configuration!', $e->getMessage());
        }
    }

    public function test_retry_handles_false_result_from_closure()
    {
        $requestMaker = new class
        {
            use MakesHttpRequests;
        };

        try {
            $requestMaker->retry(0, function () {
                return false;
            }, 0);
            $this->fail();
        } catch (TimeoutException $e) {
            $this->assertSame([], $e->output());
        }
    }

    public function test_retry_handles_null_result_from_closure()
    {
        $requestMaker = new class
        {
            use MakesHttpRequests;
        };

        try {
            $requestMaker->retry(0, function () {
                return null;
            }, 0);
            $this->fail();
        } catch (TimeoutException $e) {
            $this->assertSame([], $e->output());
        }
    }

    public function test_retry_handles_falsey_string_result_from_closure()
    {
        $requestMaker = new class
        {
            use MakesHttpRequests;
        };

        try {
            $requestMaker->retry(0, function () {
                return '';
            }, 0);
            $this->fail();
        } catch (TimeoutException $e) {
            $this->assertSame([''], $e->output());
        }
    }

    public function test_retry_handles_falsey_numer_result_from_closure()
    {
        $requestMaker = new class
        {
            use MakesHttpRequests;
        };

        try {
            $requestMaker->retry(0, function () {
                return 0;
            }, 0);
            $this->fail();
        } catch (TimeoutException $e) {
            $this->assertSame([0], $e->output());
        }
    }

    public function test_retry_handles_falsey_array_result_from_closure()
    {
        $requestMaker = new class
        {
            use MakesHttpRequests;
        };

        try {
            $requestMaker->retry(0, function () {
                return [];
            }, 0);
            $this->fail();
        } catch (TimeoutException $e) {
            $this->assertSame([], $e->output());
        }
    }

    public function test_rate_limit_exceeded_with_header_set()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $timestamp = strtotime(date('Y-m-d H:i:s'));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers', [])->andReturn(
            new Response(429, [
                'x-ratelimit-reset' => $timestamp,
            ], 'Too Many Attempts.')
        );

        try {
            $forge->servers('org-123');
        } catch (RateLimitExceededException $e) {
            $this->assertSame($timestamp, $e->rateLimitResetsAt);
        }
    }

    public function test_rate_limit_exceeded_with_header_not_available()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers', [])->andReturn(
            new Response(429, [], 'Too Many Attempts.')
        );

        try {
            $forge->servers('org-123');
        } catch (RateLimitExceededException $e) {
            $this->assertNull($e->rateLimitResetsAt);
        }
    }

    public function test_getting_server_credentials()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/server-credentials', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "cred-1", "name": "AWS Credentials"}]}')
        );

        $this->assertCount(1, $forge->serverCredentials('org-123'));
    }

    public function test_getting_single_server_credential()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/server-credentials/cred-1', [])->andReturn(
            new Response(200, [], '{"data": {"id": "cred-1", "name": "AWS Credentials"}}')
        );

        $credential = $forge->serverCredential('org-123', 'cred-1');
        $this->assertSame('cred-1', $credential->id);
    }

    public function test_getting_workers()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/sites/site-1/workers', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "worker-1", "connection": "redis"}]}')
        );

        $this->assertCount(1, $forge->workers('org-123', 'server-1', 'site-1'));
    }

    public function test_creating_worker()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/server-1/sites/site-1/workers', [
            'form_params' => ['connection' => 'redis', 'queue' => 'default'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "worker-2", "connection": "redis"}}')
        );

        $worker = $forge->createWorker('org-123', 'server-1', 'site-1', ['connection' => 'redis', 'queue' => 'default']);
        $this->assertSame('worker-2', $worker->id);
    }

    public function test_deleting_worker()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/server-1/sites/site-1/workers/worker-1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteWorker('org-123', 'server-1', 'site-1', 'worker-1');
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_forge_recipes()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'forge-recipes', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "fr-1", "name": "Install Node.js"}]}')
        );

        $this->assertCount(1, $forge->forgeRecipes());
    }

    public function test_getting_single_forge_recipe()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'forge-recipes/fr-1', [])->andReturn(
            new Response(200, [], '{"data": {"id": "fr-1", "name": "Install Node.js"}}')
        );

        $recipe = $forge->forgeRecipe('fr-1');
        $this->assertSame('fr-1', $recipe->id);
    }

    public function test_creating_recipe()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/recipes', [
            'form_params' => ['name' => 'My Recipe', 'script' => 'echo "Hello"'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "recipe-1", "name": "My Recipe"}}')
        );

        $recipe = $forge->createRecipe('org-123', ['name' => 'My Recipe', 'script' => 'echo "Hello"']);
        $this->assertSame('recipe-1', $recipe->id);
    }

    public function test_updating_recipe()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/recipes/recipe-1', [
            'form_params' => ['name' => 'Updated Recipe'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "recipe-1", "name": "Updated Recipe"}}')
        );

        $recipe = $forge->updateRecipe('org-123', 'recipe-1', ['name' => 'Updated Recipe']);
        $this->assertSame('Updated Recipe', $recipe->name);
    }

    public function test_deleting_recipe()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/recipes/recipe-1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteRecipe('org-123', 'recipe-1');
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_server_events()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/events', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "event-1", "description": "Server created"}]}')
        );

        $this->assertCount(1, $forge->serverEvents('org-123', 'server-1'));
    }

    public function test_getting_single_server_event()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/events/event-1', [])->andReturn(
            new Response(200, [], '{"data": {"id": "event-1", "description": "Server created"}}')
        );

        $event = $forge->serverEvent('org-123', 'server-1', 'event-1');
        $this->assertSame('event-1', $event->id);
    }

    public function test_getting_archived_servers()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/archives', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "server-2", "name": "Archived Server"}]}')
        );

        $this->assertCount(1, $forge->archivedServers('org-123'));
    }

    public function test_getting_php_versions()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/php/versions', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "php-1", "version": "8.3"}]}')
        );

        $this->assertCount(1, $forge->phpVersions('org-123', 'server-1'));
    }

    public function test_installing_php_version()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/server-1/php/versions', [
            'form_params' => ['version' => '8.3'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "php-2", "version": "8.3"}}')
        );

        $phpVersion = $forge->installPhpVersion('org-123', 'server-1', ['version' => '8.3']);
        $this->assertSame('php-2', $phpVersion->id);
    }

    public function test_updating_php_version()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/server-1/php/versions/php-1', [
            'form_params' => ['version' => '8.3'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "php-1", "version": "8.3"}}')
        );

        $phpVersion = $forge->updatePhpVersion('org-123', 'server-1', 'php-1', ['version' => '8.3']);
        $this->assertSame('8.3', $phpVersion->version);
    }

    public function test_deleting_php_version()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/server-1/php/versions/php-1', [])->andReturn(
            new Response(204)
        );

        $forge->deletePhpVersion('org-123', 'server-1', 'php-1');
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_all_sites()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'sites', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "site-1", "name": "example.com"}]}')
        );

        $this->assertCount(1, $forge->sites());
    }

    public function test_getting_organization_sites()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/sites', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "site-1", "name": "example.com"}]}')
        );

        $this->assertCount(1, $forge->organizationSites('org-123'));
    }

    public function test_getting_organization_site()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/sites/site-1', [])->andReturn(
            new Response(200, [], '{"data": {"id": "site-1", "name": "example.com"}}')
        );

        $site = $forge->organizationSite('org-123', 'site-1');
        $this->assertSame('site-1', $site->id);
    }
}
