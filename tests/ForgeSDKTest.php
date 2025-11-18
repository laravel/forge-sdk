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

    public function test_getting_heartbeats()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/sites/site-1/heartbeats', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "heartbeat-1", "name": "My Heartbeat", "interval": 60, "status": "active"}]}')
        );

        $heartbeats = $forge->heartbeats('org-123', 'server-1', 'site-1');
        $this->assertCount(1, $heartbeats);
        $this->assertSame('heartbeat-1', $heartbeats[0]->id);
    }

    public function test_getting_single_heartbeat()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/sites/site-1/heartbeats/heartbeat-1', [])->andReturn(
            new Response(200, [], '{"data": {"id": "heartbeat-1", "name": "My Heartbeat", "interval": 60, "status": "active"}}')
        );

        $heartbeat = $forge->heartbeat('org-123', 'server-1', 'site-1', 'heartbeat-1');
        $this->assertSame('heartbeat-1', $heartbeat->id);
        $this->assertSame('My Heartbeat', $heartbeat->name);
    }

    public function test_creating_heartbeat()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/server-1/sites/site-1/heartbeats', [
            'form_params' => ['name' => 'My Heartbeat', 'interval' => 60],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "heartbeat-1", "name": "My Heartbeat", "interval": 60, "status": "active"}}')
        );

        $heartbeat = $forge->createHeartbeat('org-123', 'server-1', 'site-1', ['name' => 'My Heartbeat', 'interval' => 60]);
        $this->assertSame('heartbeat-1', $heartbeat->id);
        $this->assertSame('My Heartbeat', $heartbeat->name);
        $this->assertSame(60, $heartbeat->interval);
    }

    public function test_updating_heartbeat()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/server-1/sites/site-1/heartbeats/heartbeat-1', [
            'form_params' => ['name' => 'Updated Heartbeat'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "heartbeat-1", "name": "Updated Heartbeat", "interval": 60, "status": "active"}}')
        );

        $heartbeat = $forge->updateHeartbeat('org-123', 'server-1', 'site-1', 'heartbeat-1', ['name' => 'Updated Heartbeat']);
        $this->assertSame('Updated Heartbeat', $heartbeat->name);
    }

    public function test_deleting_heartbeat()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/server-1/sites/site-1/heartbeats/heartbeat-1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteHeartbeat('org-123', 'server-1', 'site-1', 'heartbeat-1');
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

    public function test_getting_team_invitations()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/teams/team-1/invites', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "invite-1", "email": "user@example.com"}]}')
        );

        $this->assertCount(1, $forge->teamInvitations('org-123', 'team-1'));
    }

    public function test_getting_single_team_invitation()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/teams/team-1/invites/invite-1', [])->andReturn(
            new Response(200, [], '{"data": {"id": "invite-1", "email": "user@example.com"}}')
        );

        $invitation = $forge->teamInvitation('org-123', 'team-1', 'invite-1');
        $this->assertSame('invite-1', $invitation->id);
    }

    public function test_creating_team_invitation()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/teams/team-1/invites', [
            'form_params' => ['email' => 'newuser@example.com', 'role_id' => 'role-1'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "invite-2", "email": "newuser@example.com"}}')
        );

        $invitation = $forge->createTeamInvitation('org-123', 'team-1', ['email' => 'newuser@example.com', 'role_id' => 'role-1']);
        $this->assertSame('invite-2', $invitation->id);
    }

    public function test_deleting_team_invitation()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/teams/team-1/invites/invite-1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteTeamInvitation('org-123', 'team-1', 'invite-1');
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_team_servers()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/teams/team-1/servers', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "server-1", "name": "Production Server"}]}')
        );

        $this->assertCount(1, $forge->teamServers('org-123', 'team-1'));
    }

    public function test_creating_team_server_share()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/teams/team-1/servers', [
            'form_params' => ['server_id' => 'server-1'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "share-1", "server_id": "server-1"}}')
        );

        $share = $forge->createTeamServerShare('org-123', 'team-1', ['server_id' => 'server-1']);
        $this->assertSame('share-1', $share->id);
    }

    public function test_deleting_team_server_share()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/teams/team-1/servers/server-1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteTeamServerShare('org-123', 'team-1', 'server-1');
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_team_server_credentials()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/teams/team-1/server-credentials', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "cred-1", "name": "AWS Credentials"}]}')
        );

        $this->assertCount(1, $forge->teamServerCredentials('org-123', 'team-1'));
    }

    public function test_sharing_server_credential()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/teams/team-1/server-credentials', [
            'form_params' => ['credential_id' => 'cred-1'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "share-2", "credential_id": "cred-1"}}')
        );

        $share = $forge->shareServerCredential('org-123', 'team-1', ['credential_id' => 'cred-1']);
        $this->assertSame('share-2', $share->id);
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

    public function test_getting_reverb_integration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/sites/site-1/integrations/reverb', [])->andReturn(
            new Response(200, [], '{"data": {"id": "int-3", "status": "installed"}}')
        );

        $integration = $forge->getReverb('org-123', 'server-1', 'site-1');
        $this->assertSame('int-3', $integration->id);
    }

    public function test_creating_reverb_integration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/server-1/sites/site-1/integrations/reverb', [])->andReturn(
            new Response(200, [], '{"data": {"id": "int-3", "type": "reverb"}}')
        );

        $integration = $forge->createReverb('org-123', 'server-1', 'site-1');
        $this->assertSame('int-3', $integration->id);
    }

    public function test_deleting_reverb_integration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/server-1/sites/site-1/integrations/reverb', [])->andReturn(
            new Response(204)
        );

        $forge->deleteReverb('org-123', 'server-1', 'site-1');
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_inertia_integration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/sites/site-1/integrations/inertia', [])->andReturn(
            new Response(200, [], '{"data": {"id": "int-4", "status": "installed"}}')
        );

        $integration = $forge->getInertia('org-123', 'server-1', 'site-1');
        $this->assertSame('int-4', $integration->id);
    }

    public function test_creating_inertia_integration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/server-1/sites/site-1/integrations/inertia', [])->andReturn(
            new Response(200, [], '{"data": {"id": "int-4", "type": "inertia"}}')
        );

        $integration = $forge->createInertia('org-123', 'server-1', 'site-1');
        $this->assertSame('int-4', $integration->id);
    }

    public function test_getting_pulse_integration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/sites/site-1/integrations/pulse', [])->andReturn(
            new Response(200, [], '{"data": {"id": "int-5", "status": "installed"}}')
        );

        $integration = $forge->getPulse('org-123', 'server-1', 'site-1');
        $this->assertSame('int-5', $integration->id);
    }

    public function test_creating_pulse_integration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/server-1/sites/site-1/integrations/pulse', [])->andReturn(
            new Response(200, [], '{"data": {"id": "int-5", "type": "pulse"}}')
        );

        $integration = $forge->createPulse('org-123', 'server-1', 'site-1');
        $this->assertSame('int-5', $integration->id);
    }

    public function test_deleting_pulse_integration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/server-1/sites/site-1/integrations/pulse', [])->andReturn(
            new Response(204)
        );

        $forge->deletePulse('org-123', 'server-1', 'site-1');
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_maintenance_integration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/sites/site-1/integrations/laravel-maintenance', [])->andReturn(
            new Response(200, [], '{"data": {"id": "int-6", "status": "installed"}}')
        );

        $integration = $forge->getMaintenance('org-123', 'server-1', 'site-1');
        $this->assertSame('int-6', $integration->id);
    }

    public function test_creating_maintenance_integration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/server-1/sites/site-1/integrations/laravel-maintenance', [])->andReturn(
            new Response(200, [], '{"data": {"id": "int-6", "type": "laravel-maintenance"}}')
        );

        $integration = $forge->createMaintenance('org-123', 'server-1', 'site-1');
        $this->assertSame('int-6', $integration->id);
    }

    public function test_deleting_maintenance_integration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/server-1/sites/site-1/integrations/laravel-maintenance', [])->andReturn(
            new Response(204)
        );

        $forge->deleteMaintenance('org-123', 'server-1', 'site-1');
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_scheduler_integration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/sites/site-1/integrations/laravel-scheduler', [])->andReturn(
            new Response(200, [], '{"data": {"id": "int-7", "status": "installed"}}')
        );

        $integration = $forge->getScheduler('org-123', 'server-1', 'site-1');
        $this->assertSame('int-7', $integration->id);
    }

    public function test_creating_scheduler_integration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/server-1/sites/site-1/integrations/laravel-scheduler', [])->andReturn(
            new Response(200, [], '{"data": {"id": "int-7", "type": "laravel-scheduler"}}')
        );

        $integration = $forge->createScheduler('org-123', 'server-1', 'site-1');
        $this->assertSame('int-7', $integration->id);
    }

    public function test_deleting_scheduler_integration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/server-1/sites/site-1/integrations/laravel-scheduler', [])->andReturn(
            new Response(204)
        );

        $forge->deleteScheduler('org-123', 'server-1', 'site-1');
        $this->assertTrue(true); // Assertion to avoid risky test warning
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

    public function test_getting_domain_configurations()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/sites/site-1/domains/domain-1/configurations', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "config-1", "type": "nginx"}]}')
        );

        $this->assertCount(1, $forge->domainConfigurations('org-123', 'server-1', 'site-1', 'domain-1'));
    }

    public function test_creating_domain_action()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/server-1/sites/site-1/domains/domain-1/actions', [
            'form_params' => ['action' => 'verify'],
        ])->andReturn(
            new Response(202, [], '{"data": {"id": "action-1", "status": "pending"}}')
        );

        $action = $forge->createDomainAction('org-123', 'server-1', 'site-1', 'domain-1', ['action' => 'verify']);
        $this->assertSame('action-1', $action['data']['id']);
    }

    public function test_creating_domain_certificate_action()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/server-1/sites/site-1/domains/domain-1/certificate/actions', [
            'form_params' => ['action' => 'renew'],
        ])->andReturn(
            new Response(202, [], '{"data": {"id": "cert-action-1", "status": "pending"}}')
        );

        $action = $forge->createDomainCertificateAction('org-123', 'server-1', 'site-1', 'domain-1', ['action' => 'renew']);
        $this->assertSame('cert-action-1', $action['data']['id']);
    }

    // Deployments - Webhooks (4 tests)

    public function test_getting_webhooks()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/srv-123/sites/site-456/webhooks', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "webhook-1", "url": "https://example.com/webhook"}]}')
        );

        $this->assertCount(1, $forge->webhooks('org-123', 'srv-123', 'site-456'));
    }

    public function test_getting_single_webhook()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/srv-123/sites/site-456/webhooks/webhook-1', [])->andReturn(
            new Response(200, [], '{"data": {"id": "webhook-1", "url": "https://example.com/webhook"}}')
        );

        $webhook = $forge->webhook('org-123', 'srv-123', 'site-456', 'webhook-1');
        $this->assertSame('webhook-1', $webhook->id);
    }

    public function test_creating_webhook()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/srv-123/sites/site-456/webhooks', [
            'form_params' => ['url' => 'https://example.com/webhook'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "webhook-2", "url": "https://example.com/webhook"}}')
        );

        $webhook = $forge->createWebhook('org-123', 'srv-123', 'site-456', ['url' => 'https://example.com/webhook']);
        $this->assertSame('webhook-2', $webhook->id);
    }

    public function test_deleting_webhook()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/srv-123/sites/site-456/webhooks/webhook-1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteWebhook('org-123', 'srv-123', 'site-456', 'webhook-1');
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    // Deployments - Main (3 tests)

    public function test_getting_deployments()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/srv-123/sites/site-456/deployments', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "deploy-1", "status": "finished"}]}')
        );

        $this->assertCount(1, $forge->deployments('org-123', 'srv-123', 'site-456'));
    }

    public function test_getting_single_deployment()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/srv-123/sites/site-456/deployments/deploy-1', [])->andReturn(
            new Response(200, [], '{"data": {"id": "deploy-1", "status": "finished"}}')
        );

        $deployment = $forge->deployment('org-123', 'srv-123', 'site-456', 'deploy-1');
        $this->assertSame('deploy-1', $deployment->id);
    }

    public function test_creating_deployment()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/srv-123/sites/site-456/deployments', [])->andReturn(
            new Response(200, [], '{"data": {"id": "deploy-2", "status": "pending"}}')
        );

        $deployment = $forge->createDeployment('org-123', 'srv-123', 'site-456');
        $this->assertSame('deploy-2', $deployment->id);
    }

    // Deployment Status (2 tests)

    public function test_getting_deployment_status()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/srv-123/sites/site-456/deployments/status', [])->andReturn(
            new Response(200, [], '{"data": {"is_deploying": false, "last_deployment_status": "finished"}}')
        );

        $status = $forge->deploymentStatus('org-123', 'srv-123', 'site-456');
        $this->assertFalse($status['is_deploying']);
    }

    public function test_updating_deployment_state()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/srv-123/sites/site-456/deployments/status', [])->andReturn(
            new Response(204)
        );

        $forge->updateDeploymentState('org-123', 'srv-123', 'site-456');
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    // Deployment Script (2 tests)

    public function test_getting_deployment_script()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/srv-123/sites/site-456/deployments/script', [])->andReturn(
            new Response(200, [], '{"data": {"script": "cd /home/forge/example.com\ngit pull origin main"}}')
        );

        $script = $forge->deploymentScript('org-123', 'srv-123', 'site-456');
        $this->assertStringContainsString('git pull', $script);
    }

    public function test_updating_deployment_script()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/srv-123/sites/site-456/deployments/script', [
            'form_params' => ['script' => 'cd /home/forge/example.com\ngit pull origin main\nphp artisan migrate'],
        ])->andReturn(
            new Response(204)
        );

        $forge->updateDeploymentScript('org-123', 'srv-123', 'site-456', ['script' => 'cd /home/forge/example.com\ngit pull origin main\nphp artisan migrate']);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    // Deployment Trigger (2 tests)

    public function test_getting_deployment_trigger_url()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/srv-123/sites/site-456/deployments/deploy-hook', [])->andReturn(
            new Response(200, [], '{"data": {"url": "https://forge.laravel.com/servers/srv-123/sites/site-456/deploy/http?token=abc123"}}')
        );

        $url = $forge->deploymentTriggerUrl('org-123', 'srv-123', 'site-456');
        $this->assertStringContainsString('deploy/http', $url);
    }

    public function test_updating_deployment_trigger_url()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/srv-123/sites/site-456/deployments/deploy-hook', [
            'form_params' => ['regenerate' => true],
        ])->andReturn(
            new Response(204)
        );

        $forge->updateDeploymentTriggerUrl('org-123', 'srv-123', 'site-456', ['regenerate' => true]);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    // Push to Deploy (2 tests)

    public function test_creating_push_to_deploy()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/srv-123/sites/site-456/deployments/push-to-deploy', [
            'form_params' => ['provider' => 'github', 'repository' => 'user/repo'],
        ])->andReturn(
            new Response(204)
        );

        $forge->createPushToDeploy('org-123', 'srv-123', 'site-456', ['provider' => 'github', 'repository' => 'user/repo']);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_deleting_push_to_deploy()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/srv-123/sites/site-456/deployments/push-to-deploy', [])->andReturn(
            new Response(204)
        );

        $forge->deletePushToDeploy('org-123', 'srv-123', 'site-456');
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    // Deployment Log (1 test)

    public function test_getting_deployment_log()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/srv-123/sites/site-456/deployments/deploy-1/log', [])->andReturn(
            new Response(200, [], '{"data": {"output": "Cloning repository...\nInstalling dependencies...\nDeployment finished successfully."}}')
        );

        $log = $forge->deploymentLog('org-123', 'srv-123', 'site-456', 'deploy-1');
        $this->assertStringContainsString('Deployment finished successfully', $log);
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

    public function test_getting_vpcs()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/server-credentials/cred-1/regions/us-east-1/vpcs', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "vpc-1", "name": "Production VPC"}]}')
        );

        $this->assertCount(1, $forge->vpcs('org-123', 'cred-1', 'us-east-1'));
    }

    public function test_getting_single_vpc()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/server-credentials/cred-1/regions/us-east-1/vpcs/vpc-1', [])->andReturn(
            new Response(200, [], '{"data": {"id": "vpc-1", "name": "Production VPC"}}')
        );

        $vpc = $forge->vpc('org-123', 'cred-1', 'us-east-1', 'vpc-1');
        $this->assertSame('vpc-1', $vpc->id);
    }

    public function test_creating_vpc()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/server-credentials/cred-1/regions/us-east-1/vpcs', [
            'form_params' => ['name' => 'New VPC', 'cidr_block' => '10.0.0.0/16'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "vpc-2", "name": "New VPC"}}')
        );

        $vpc = $forge->createVpc('org-123', 'cred-1', 'us-east-1', ['name' => 'New VPC', 'cidr_block' => '10.0.0.0/16']);
        $this->assertSame('vpc-2', $vpc->id);
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

    public function test_creating_worker_action()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/server-1/sites/site-1/workers/worker-1/actions', [
            'form_params' => ['action' => 'restart'],
        ])->andReturn(
            new Response(202, [], '{"data": {"id": "action-1", "status": "pending"}}')
        );

        $action = $forge->createWorkerAction('org-123', 'server-1', 'site-1', 'worker-1', ['action' => 'restart']);
        $this->assertSame('action-1', $action['data']['id']);
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

    public function test_getting_recipe_runs()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/recipes/recipe-1/runs', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "run-1", "status": "completed"}]}')
        );

        $this->assertCount(1, $forge->recipeRuns('org-123', 'recipe-1'));
    }

    public function test_getting_single_recipe_run()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/recipes/recipe-1/runs/run-1', [])->andReturn(
            new Response(200, [], '{"data": {"id": "run-1", "status": "completed"}}')
        );

        $run = $forge->recipeRun('org-123', 'recipe-1', 'run-1');
        $this->assertSame('run-1', $run->id);
    }

    public function test_creating_recipe_run()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/recipes/recipe-1/runs', [
            'form_params' => ['server_id' => 'server-1'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "run-2", "status": "pending"}}')
        );

        $run = $forge->createRecipeRun('org-123', 'recipe-1', ['server_id' => 'server-1']);
        $this->assertSame('run-2', $run->id);
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

    public function test_creating_archived_server()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/archives', [
            'form_params' => ['server_id' => 'server-1'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "archive-1", "server_id": "server-1"}}')
        );

        $archive = $forge->createArchivedServer('org-123', ['server_id' => 'server-1']);
        $this->assertSame('archive-1', $archive->id);
    }

    public function test_deleting_archived_server()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/archives/server-1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteArchivedServer('org-123', 'server-1');
        $this->assertTrue(true); // Assertion to avoid risky test warning
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

    public function test_getting_php_cli_version()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/php/cli-version', [])->andReturn(
            new Response(200, [], '{"data": {"version": "php83", "displayVersion": "PHP 8.3"}}')
        );

        $cliVersion = $forge->phpCliVersion('org-123', 'server-1');
        $this->assertIsArray($cliVersion);
        $this->assertSame('php83', $cliVersion['data']['version']);
        $this->assertSame('PHP 8.3', $cliVersion['data']['displayVersion']);
    }

    public function test_updating_php_cli_version()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/server-1/php/cli-version', [
            'form_params' => ['version' => 'php84'],
        ])->andReturn(
            new Response(200, [], '{"data": {"version": "php84", "displayVersion": "PHP 8.4"}}')
        );

        $cliVersion = $forge->updatePhpCliVersion('org-123', 'server-1', ['version' => 'php84']);
        $this->assertIsArray($cliVersion);
        $this->assertSame('php84', $cliVersion['data']['version']);
        $this->assertSame('PHP 8.4', $cliVersion['data']['displayVersion']);
    }

    public function test_getting_php_site_version()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/php/site-version', [])->andReturn(
            new Response(200, [], '{"data": {"version": "php83", "displayVersion": "PHP 8.3"}}')
        );

        $siteVersion = $forge->phpSiteVersion('org-123', 'server-1');
        $this->assertIsArray($siteVersion);
        $this->assertSame('php83', $siteVersion['data']['version']);
        $this->assertSame('PHP 8.3', $siteVersion['data']['displayVersion']);
    }

    public function test_updating_php_site_version()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/server-1/php/site-version', [
            'form_params' => ['version' => 'php84'],
        ])->andReturn(
            new Response(200, [], '{"data": {"version": "php84", "displayVersion": "PHP 8.4"}}')
        );

        $siteVersion = $forge->updatePhpSiteVersion('org-123', 'server-1', ['version' => 'php84']);
        $this->assertIsArray($siteVersion);
        $this->assertSame('php84', $siteVersion['data']['version']);
        $this->assertSame('PHP 8.4', $siteVersion['data']['displayVersion']);
    }

    public function test_getting_php_fpm_config()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/php/versions/php83/configs/fpm', [])->andReturn(
            new Response(200, [], 'pm = dynamic')
        );

        $config = $forge->phpFpmConfig('org-123', 'server-1', 'php83');
        $this->assertIsString($config);
        $this->assertStringContainsString('pm = dynamic', $config);
    }

    public function test_updating_php_fpm_config()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/server-1/php/versions/php83/configs/fpm', [
            'form_params' => ['content' => 'pm = ondemand'],
        ])->andReturn(
            new Response(200, [], 'pm = ondemand')
        );

        $config = $forge->updatePhpFpmConfig('org-123', 'server-1', 'php83', ['content' => 'pm = ondemand']);
        $this->assertIsString($config);
        $this->assertStringContainsString('pm = ondemand', $config);
    }

    public function test_getting_php_cli_config()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/php/versions/php83/configs/cli', [])->andReturn(
            new Response(200, [], 'memory_limit = 256M')
        );

        $config = $forge->phpCliConfig('org-123', 'server-1', 'php83');
        $this->assertIsString($config);
        $this->assertStringContainsString('memory_limit = 256M', $config);
    }

    public function test_updating_php_cli_config()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/server-1/php/versions/php83/configs/cli', [
            'form_params' => ['content' => 'memory_limit = 512M'],
        ])->andReturn(
            new Response(200, [], 'memory_limit = 512M')
        );

        $config = $forge->updatePhpCliConfig('org-123', 'server-1', 'php83', ['content' => 'memory_limit = 512M']);
        $this->assertIsString($config);
        $this->assertStringContainsString('memory_limit = 512M', $config);
    }

    public function test_getting_php_pool_config()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/php/versions/php83/configs/pool', [])->andReturn(
            new Response(200, [], 'pm.max_children = 50')
        );

        $config = $forge->phpPoolConfig('org-123', 'server-1', 'php83');
        $this->assertIsString($config);
        $this->assertStringContainsString('pm.max_children = 50', $config);
    }

    public function test_updating_php_pool_config()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/server-1/php/versions/php83/configs/pool', [
            'form_params' => ['content' => 'pm.max_children = 100'],
        ])->andReturn(
            new Response(200, [], 'pm.max_children = 100')
        );

        $config = $forge->updatePhpPoolConfig('org-123', 'server-1', 'php83', ['content' => 'pm.max_children = 100']);
        $this->assertIsString($config);
        $this->assertStringContainsString('pm.max_children = 100', $config);
    }

    public function test_getting_php_max_upload_size()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/php/max-upload-size', [])->andReturn(
            new Response(200, [], '{"data": {"size": "256M"}}')
        );

        $uploadSize = $forge->phpMaxUploadSize('org-123', 'server-1');
        $this->assertIsArray($uploadSize);
        $this->assertSame('256M', $uploadSize['data']['size']);
    }

    public function test_updating_php_max_upload_size()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/server-1/php/max-upload-size', [
            'form_params' => ['size' => '512M'],
        ])->andReturn(
            new Response(200, [], '{"data": {"size": "512M"}}')
        );

        $uploadSize = $forge->updatePhpMaxUploadSize('org-123', 'server-1', ['size' => '512M']);
        $this->assertIsArray($uploadSize);
        $this->assertSame('512M', $uploadSize['data']['size']);
    }

    public function test_getting_php_max_execution_time()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/php/max-execution-time', [])->andReturn(
            new Response(200, [], '{"data": {"time": "60"}}')
        );

        $executionTime = $forge->phpMaxExecutionTime('org-123', 'server-1');
        $this->assertIsArray($executionTime);
        $this->assertSame('60', $executionTime['data']['time']);
    }

    public function test_updating_php_max_execution_time()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/server-1/php/max-execution-time', [
            'form_params' => ['time' => '120'],
        ])->andReturn(
            new Response(200, [], '{"data": {"time": "120"}}')
        );

        $executionTime = $forge->updatePhpMaxExecutionTime('org-123', 'server-1', ['time' => '120']);
        $this->assertIsArray($executionTime);
        $this->assertSame('120', $executionTime['data']['time']);
    }

    public function test_getting_php_opcache()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/php/opcache', [])->andReturn(
            new Response(200, [], '{"data": {"status": "enabled", "memory": "128"}}')
        );

        $opcache = $forge->phpOpcache('org-123', 'server-1');
        $this->assertIsArray($opcache);
        $this->assertSame('enabled', $opcache['data']['status']);
        $this->assertSame('128', $opcache['data']['memory']);
    }

    public function test_creating_php_opcache()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/server-1/php/opcache', [
            'form_params' => ['memory' => '256'],
        ])->andReturn(
            new Response(200, [], '{"data": {"status": "enabled", "memory": "256"}}')
        );

        $opcache = $forge->createPhpOpcache('org-123', 'server-1', ['memory' => '256']);
        $this->assertIsArray($opcache);
        $this->assertSame('enabled', $opcache['data']['status']);
        $this->assertSame('256', $opcache['data']['memory']);
    }

    public function test_deleting_php_opcache()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/server-1/php/opcache', [])->andReturn(
            new Response(204)
        );

        $forge->deletePhpOpcache('org-123', 'server-1');
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

    // Scheduled Jobs (5 tests)

    public function test_getting_scheduled_jobs()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/scheduled-jobs', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "job-1", "command": "php artisan schedule:run", "frequency": "hourly"}]}')
        );

        $this->assertCount(1, $forge->scheduledJobs('org-123', 'server-1'));
    }

    public function test_getting_single_scheduled_job()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/scheduled-jobs/job-1', [])->andReturn(
            new Response(200, [], '{"data": {"id": "job-1", "command": "php artisan schedule:run", "frequency": "hourly"}}')
        );

        $job = $forge->scheduledJob('org-123', 'server-1', 'job-1');
        $this->assertSame('job-1', $job->id);
    }

    public function test_creating_scheduled_job()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/server-1/scheduled-jobs', [
            'form_params' => ['command' => 'php artisan queue:work', 'frequency' => 'daily', 'user' => 'forge'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "job-2", "command": "php artisan queue:work", "frequency": "daily", "user": "forge"}}')
        );

        $job = $forge->createScheduledJob('org-123', 'server-1', ['command' => 'php artisan queue:work', 'frequency' => 'daily', 'user' => 'forge']);
        $this->assertSame('job-2', $job->id);
    }

    public function test_deleting_scheduled_job()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/server-1/scheduled-jobs/job-1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteScheduledJob('org-123', 'server-1', 'job-1');
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_scheduled_job_output()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/scheduled-jobs/job-1/output', [])->andReturn(
            new Response(200, [], '{"data": {"output": "Job started at 2025-11-18 10:00:00\nProcessing items...\nJob completed successfully."}}')
        );

        $output = $forge->scheduledJobOutput('org-123', 'server-1', 'job-1');
        $this->assertStringContainsString('Job completed successfully', $output);
    }

    // Firewall Rules (4 tests)

    public function test_getting_firewall_rules()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/firewall-rules', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "rule-1", "name": "SSH Access", "port": "22"}]}')
        );

        $this->assertCount(1, $forge->firewallRules('org-123', 'server-1'));
    }

    public function test_getting_single_firewall_rule()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/firewall-rules/rule-1', [])->andReturn(
            new Response(200, [], '{"data": {"id": "rule-1", "name": "SSH Access", "port": "22"}}')
        );

        $rule = $forge->firewallRule('org-123', 'server-1', 'rule-1');
        $this->assertSame('rule-1', $rule->id);
    }

    public function test_creating_firewall_rule()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/server-1/firewall-rules', [
            'form_params' => ['name' => 'HTTP Access', 'port' => '80'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "rule-2", "name": "HTTP Access", "port": "80"}}')
        );

        $rule = $forge->createFirewallRule('org-123', 'server-1', ['name' => 'HTTP Access', 'port' => '80']);
        $this->assertSame('rule-2', $rule->id);
    }

    public function test_deleting_firewall_rule()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/server-1/firewall-rules/rule-1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteFirewallRule('org-123', 'server-1', 'rule-1');
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    // Monitors (4 tests)

    public function test_getting_monitors()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/monitors', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "monitor-1", "type": "cpu", "threshold": "80"}]}')
        );

        $this->assertCount(1, $forge->monitors('org-123', 'server-1'));
    }

    public function test_getting_single_monitor()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/monitors/monitor-1', [])->andReturn(
            new Response(200, [], '{"data": {"id": "monitor-1", "type": "cpu", "threshold": "80"}}')
        );

        $monitor = $forge->monitor('org-123', 'server-1', 'monitor-1');
        $this->assertSame('monitor-1', $monitor->id);
    }

    public function test_creating_monitor()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/server-1/monitors', [
            'form_params' => ['type' => 'disk', 'threshold' => '90'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "monitor-2", "type": "disk", "threshold": "90"}}')
        );

        $monitor = $forge->createMonitor('org-123', 'server-1', ['type' => 'disk', 'threshold' => '90']);
        $this->assertSame('monitor-2', $monitor->id);
    }

    public function test_deleting_monitor()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/server-1/monitors/monitor-1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteMonitor('org-123', 'server-1', 'monitor-1');
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    // SSH Keys (6 tests)

    public function test_getting_ssh_keys()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/ssh-keys', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "key-1", "name": "Deploy Key", "username": "forge"}]}')
        );

        $this->assertCount(1, $forge->sshKeys('org-123', 'server-1'));
    }

    public function test_getting_single_ssh_key()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/ssh-keys/key-1', [])->andReturn(
            new Response(200, [], '{"data": {"id": "key-1", "name": "Deploy Key", "username": "forge"}}')
        );

        $key = $forge->sshKey('org-123', 'server-1', 'key-1');
        $this->assertSame('key-1', $key->id);
    }

    public function test_creating_ssh_key()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/server-1/ssh-keys', [
            'form_params' => ['name' => 'Production Key', 'key' => 'ssh-rsa AAAAB3...'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "key-2", "name": "Production Key", "username": "forge"}}')
        );

        $key = $forge->createSshKey('org-123', 'server-1', ['name' => 'Production Key', 'key' => 'ssh-rsa AAAAB3...']);
        $this->assertSame('key-2', $key->id);
    }

    public function test_deleting_ssh_key()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/server-1/ssh-keys/key-1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteSshKey('org-123', 'server-1', 'key-1');
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_server_public_key()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/key', [])->andReturn(
            new Response(200, [], '{"data": {"public_key": "ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQC..."}}')
        );

        $publicKey = $forge->serverPublicKey('org-123', 'server-1');
        $this->assertStringContainsString('ssh-rsa', $publicKey);
    }

    public function test_updating_server_public_key()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/server-1/key', [
            'form_params' => ['public_key' => 'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQD...'],
        ])->andReturn(
            new Response(200, [], '{"data": {"public_key": "ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQD..."}}')
        );

        $publicKey = $forge->updateServerPublicKey('org-123', 'server-1', ['public_key' => 'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQD...']);
        $this->assertStringContainsString('ssh-rsa', $publicKey);
    }

    // Nginx Templates (5 tests)

    public function test_getting_nginx_templates()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/nginx/templates', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "template-1", "name": "Laravel Template", "content": "server { listen 80; }"}]}')
        );

        $this->assertCount(1, $forge->nginxTemplates('org-123', 'server-1'));
    }

    public function test_getting_single_nginx_template()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/nginx/templates/template-1', [])->andReturn(
            new Response(200, [], '{"data": {"id": "template-1", "name": "Laravel Template", "content": "server { listen 80; }"}}')
        );

        $template = $forge->nginxTemplate('org-123', 'server-1', 'template-1');
        $this->assertSame('template-1', $template->id);
    }

    public function test_creating_nginx_template()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/server-1/nginx/templates', [
            'form_params' => ['name' => 'Custom Template', 'content' => 'server { listen 443 ssl; }'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "template-2", "name": "Custom Template", "content": "server { listen 443 ssl; }"}}')
        );

        $template = $forge->createNginxTemplate('org-123', 'server-1', ['name' => 'Custom Template', 'content' => 'server { listen 443 ssl; }']);
        $this->assertSame('template-2', $template->id);
    }

    public function test_updating_nginx_template()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/server-1/nginx/templates/template-1', [
            'form_params' => ['content' => 'server { listen 8080; }'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "template-1", "name": "Laravel Template", "content": "server { listen 8080; }"}}')
        );

        $template = $forge->updateNginxTemplate('org-123', 'server-1', 'template-1', ['content' => 'server { listen 8080; }']);
        $this->assertSame('server { listen 8080; }', $template->content);
    }

    public function test_deleting_nginx_template()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/server-1/nginx/templates/template-1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteNginxTemplate('org-123', 'server-1', 'template-1');
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    // Security Rules (5 tests)

    public function test_getting_security_rules()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/sites/site-1/security-rules', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "rule-1", "name": "Block Bad Bots", "path": "/admin"}]}')
        );

        $this->assertCount(1, $forge->securityRules('org-123', 'server-1', 'site-1'));
    }

    public function test_getting_single_security_rule()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/sites/site-1/security-rules/rule-1', [])->andReturn(
            new Response(200, [], '{"data": {"id": "rule-1", "name": "Block Bad Bots", "path": "/admin"}}')
        );

        $rule = $forge->securityRule('org-123', 'server-1', 'site-1', 'rule-1');
        $this->assertSame('rule-1', $rule->id);
    }

    public function test_creating_security_rule()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/server-1/sites/site-1/security-rules', [
            'form_params' => ['name' => 'Rate Limit', 'path' => '/api', 'rule' => 'limit_req'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "rule-2", "name": "Rate Limit", "path": "/api", "rule": "limit_req"}}')
        );

        $rule = $forge->createSecurityRule('org-123', 'server-1', 'site-1', ['name' => 'Rate Limit', 'path' => '/api', 'rule' => 'limit_req']);
        $this->assertSame('rule-2', $rule->id);
    }

    public function test_updating_security_rule()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/server-1/sites/site-1/security-rules/rule-1', [
            'form_params' => ['path' => '/admin/*'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "rule-1", "name": "Block Bad Bots", "path": "/admin/*"}}')
        );

        $rule = $forge->updateSecurityRule('org-123', 'server-1', 'site-1', 'rule-1', ['path' => '/admin/*']);
        $this->assertSame('/admin/*', $rule->path);
    }

    public function test_deleting_security_rule()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/server-1/sites/site-1/security-rules/rule-1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteSecurityRule('org-123', 'server-1', 'site-1', 'rule-1');
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    // Redirect Rules (4 tests)

    public function test_getting_redirect_rules()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/sites/site-1/redirect-rules', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "redirect-1", "from": "/old-path", "to": "/new-path", "type": "permanent"}]}')
        );

        $this->assertCount(1, $forge->redirectRules('org-123', 'server-1', 'site-1'));
    }

    public function test_getting_single_redirect_rule()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/sites/site-1/redirect-rules/redirect-1', [])->andReturn(
            new Response(200, [], '{"data": {"id": "redirect-1", "from": "/old-path", "to": "/new-path", "type": "permanent"}}')
        );

        $rule = $forge->redirectRule('org-123', 'server-1', 'site-1', 'redirect-1');
        $this->assertSame('redirect-1', $rule->id);
    }

    public function test_creating_redirect_rule()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/server-1/sites/site-1/redirect-rules', [
            'form_params' => ['from' => '/blog', 'to' => '/articles', 'type' => 'redirect'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "redirect-2", "from": "/blog", "to": "/articles", "type": "redirect"}}')
        );

        $rule = $forge->createRedirectRule('org-123', 'server-1', 'site-1', ['from' => '/blog', 'to' => '/articles', 'type' => 'redirect']);
        $this->assertSame('redirect-2', $rule->id);
    }

    public function test_deleting_redirect_rule()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/server-1/sites/site-1/redirect-rules/redirect-1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteRedirectRule('org-123', 'server-1', 'site-1', 'redirect-1');
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    // Commands (5 tests)

    public function test_getting_commands()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/sites/site-1/commands', [])->andReturn(
            new Response(200, [], '{"data": [{"id": "cmd-1", "command": "php artisan migrate", "status": "finished"}]}')
        );

        $this->assertCount(1, $forge->commands('org-123', 'server-1', 'site-1'));
    }

    public function test_getting_single_command()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/sites/site-1/commands/cmd-1', [])->andReturn(
            new Response(200, [], '{"data": {"id": "cmd-1", "command": "php artisan migrate", "status": "finished"}}')
        );

        $command = $forge->command('org-123', 'server-1', 'site-1', 'cmd-1');
        $this->assertSame('cmd-1', $command->id);
    }

    public function test_creating_command()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/server-1/sites/site-1/commands', [
            'form_params' => ['command' => 'php artisan cache:clear'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": "cmd-2", "command": "php artisan cache:clear", "status": "running"}}')
        );

        $command = $forge->createCommand('org-123', 'server-1', 'site-1', ['command' => 'php artisan cache:clear']);
        $this->assertSame('cmd-2', $command->id);
    }

    public function test_deleting_command()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/server-1/sites/site-1/commands/cmd-1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteCommand('org-123', 'server-1', 'site-1', 'cmd-1');
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_command_output()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/sites/site-1/commands/cmd-1/output', [])->andReturn(
            new Response(200, [], '{"data": {"output": "Running migrations...\nMigration table created successfully.\nMigrating: 2024_01_01_000000_create_users_table\nMigrated: 2024_01_01_000000_create_users_table (45.67ms)"}}')
        );

        $output = $forge->commandOutput('org-123', 'server-1', 'site-1', 'cmd-1');
        $this->assertStringContainsString('Running migrations', $output);
    }

    // Site Configuration (6 tests)

    public function test_getting_site_environment()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/sites/site-1/environment', [])->andReturn(
            new Response(200, [], '{"data": {"content": "APP_NAME=Laravel\nAPP_ENV=production\nAPP_KEY=base64:randomkey123\nAPP_DEBUG=false\nAPP_URL=https://example.com\n\nDB_CONNECTION=mysql\nDB_HOST=127.0.0.1\nDB_PORT=3306\nDB_DATABASE=laravel\nDB_USERNAME=forge\nDB_PASSWORD=secret"}}')
        );

        $content = $forge->siteEnvironment('org-123', 'server-1', 'site-1');
        $this->assertStringContainsString('APP_NAME=Laravel', $content);
        $this->assertStringContainsString('DB_CONNECTION=mysql', $content);
    }

    public function test_updating_site_environment()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $content = "APP_NAME=MyApp\nAPP_ENV=production\nAPP_KEY=base64:newkey456";

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/server-1/sites/site-1/environment', [
            'form_params' => ['content' => $content],
        ])->andReturn(
            new Response(200)
        );

        $forge->updateSiteEnvironment('org-123', 'server-1', 'site-1', $content);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_site_nginx()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/sites/site-1/nginx', [])->andReturn(
            new Response(200, [], "{\"data\": {\"content\": \"server {\\n    listen 80;\\n    server_name example.com;\\n    root /home/forge/example.com;\\n\\n    location / {\\n        try_files \$uri \$uri/ /index.php?\$query_string;\\n    }\\n}\"}}")
        );

        $content = $forge->siteNginx('org-123', 'server-1', 'site-1');
        $this->assertStringContainsString('server_name example.com', $content);
        $this->assertStringContainsString('location / {', $content);
    }

    public function test_updating_site_nginx()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $content = "server {\n    listen 80;\n    server_name example.com;\n}";

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/server-1/sites/site-1/nginx', [
            'form_params' => ['content' => $content],
        ])->andReturn(
            new Response(200)
        );

        $forge->updateSiteNginx('org-123', 'server-1', 'site-1', $content);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_site_php()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/sites/site-1/php', [])->andReturn(
            new Response(200, [], '{"data": {"version": "php83", "displayVersion": "PHP 8.3"}}')
        );

        $phpData = $forge->sitePhp('org-123', 'server-1', 'site-1');
        $this->assertIsArray($phpData);
        $this->assertSame('php83', $phpData['version']);
        $this->assertSame('PHP 8.3', $phpData['displayVersion']);
    }

    public function test_updating_site_php()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/server-1/sites/site-1/php', [
            'form_params' => ['version' => 'php84'],
        ])->andReturn(
            new Response(200)
        );

        $forge->updateSitePhp('org-123', 'server-1', 'site-1', ['version' => 'php84']);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    // Server Logs (2 tests)

    public function test_getting_server_log()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/logs/nginx-error', [])->andReturn(
            new Response(200, [], '{"data": {"content": "2025/11/18 10:00:00 [error] 1234#1234: *1 connect() failed (111: Connection refused)\n2025/11/18 10:01:00 [warn] 1234#1234: *2 upstream server temporarily disabled\n2025/11/18 10:02:00 [error] 1234#1234: *3 open() \\"/var/www/html/favicon.ico\\" failed (2: No such file or directory)"}}')
        );

        $log = $forge->serverLog('org-123', 'server-1', 'nginx-error');
        $this->assertStringContainsString('Connection refused', $log);
    }

    public function test_deleting_server_log()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/server-1/logs/nginx-error', [])->andReturn(
            new Response(204)
        );

        $forge->deleteServerLog('org-123', 'server-1', 'nginx-error');
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    // Site Logs (6 tests)

    public function test_getting_site_nginx_access_log()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/sites/site-1/logs/nginx-access', [])->andReturn(
            new Response(200, [], '{"data": {"content": "192.168.1.1 - - [18/Nov/2025:10:00:00 +0000] \\"GET /api/users HTTP/1.1\\" 200 1234 \\"-\\" \\"Mozilla/5.0\\"\n192.168.1.2 - - [18/Nov/2025:10:01:00 +0000] \\"POST /api/login HTTP/1.1\\" 201 567 \\"-\\" \\"axios/1.6.0\\"\n192.168.1.3 - - [18/Nov/2025:10:02:00 +0000] \\"GET /health HTTP/1.1\\" 200 89 \\"-\\" \\"HealthCheck/1.0\\""}}')
        );

        $log = $forge->siteLog('org-123', 'server-1', 'site-1', 'nginx-access');
        $this->assertStringContainsString('GET /api/users', $log);
    }

    public function test_deleting_site_nginx_access_log()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/server-1/sites/site-1/logs/nginx-access', [])->andReturn(
            new Response(204)
        );

        $forge->deleteSiteLog('org-123', 'server-1', 'site-1', 'nginx-access');
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_site_nginx_error_log()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/sites/site-1/logs/nginx-error', [])->andReturn(
            new Response(200, [], '{"data": {"content": "2025/11/18 10:00:00 [error] 5678#5678: *10 FastCGI sent in stderr: \\"PHP message: PHP Fatal error: Uncaught Exception\\"\n2025/11/18 10:01:00 [error] 5678#5678: *11 connect() to unix:/var/run/php/php8.3-fpm.sock failed (2: No such file or directory)\n2025/11/18 10:02:00 [warn] 5678#5678: *12 an upstream response is buffered to a temporary file"}}')
        );

        $log = $forge->siteLog('org-123', 'server-1', 'site-1', 'nginx-error');
        $this->assertStringContainsString('FastCGI sent in stderr', $log);
    }

    public function test_deleting_site_nginx_error_log()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/server-1/sites/site-1/logs/nginx-error', [])->andReturn(
            new Response(204)
        );

        $forge->deleteSiteLog('org-123', 'server-1', 'site-1', 'nginx-error');
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_site_application_log()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/server-1/sites/site-1/logs/application', [])->andReturn(
            new Response(200, [], '{"data": {"content": "[2025-11-18 10:00:00] production.ERROR: SQLSTATE[HY000] [1045] Access denied for user\n[2025-11-18 10:01:00] production.INFO: User login successful {\\"user_id\\": 123}\n[2025-11-18 10:02:00] production.WARNING: Cache store redis is not available"}}')
        );

        $log = $forge->siteLog('org-123', 'server-1', 'site-1', 'application');
        $this->assertStringContainsString('User login successful', $log);
    }

    public function test_deleting_site_application_log()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/server-1/sites/site-1/logs/application', [])->andReturn(
            new Response(204)
        );

        $forge->deleteSiteLog('org-123', 'server-1', 'site-1', 'application');
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    // Server Actions (8 tests)

    public function test_creating_server_action()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/server-1/actions', [
            'form_params' => ['action' => 'reboot'],
        ])->andReturn(
            new Response(202, [], '{"data": {"id": "action-1", "status": "pending"}}')
        );

        $action = $forge->createServerAction('org-123', 'server-1', ['action' => 'reboot']);
        $this->assertSame('action-1', $action['data']['id']);
        $this->assertSame('pending', $action['data']['status']);
    }

    public function test_performing_background_process_action()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/server-1/background-processes/process-1/actions', [
            'form_params' => ['action' => 'restart'],
        ])->andReturn(
            new Response(202, [], '{"data": {"id": "action-2", "status": "pending"}}')
        );

        $action = $forge->performBackgroundProcessAction('org-123', 'server-1', 'process-1', ['action' => 'restart']);
        $this->assertSame('action-2', $action['data']['id']);
        $this->assertSame('pending', $action['data']['status']);
    }

    public function test_performing_nginx_action()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/server-1/services/nginx/actions', [
            'form_params' => ['action' => 'restart'],
        ])->andReturn(
            new Response(202, [], '{"data": {"id": "action-3", "status": "pending"}}')
        );

        $action = $forge->performNginxAction('org-123', 'server-1', ['action' => 'restart']);
        $this->assertSame('action-3', $action['data']['id']);
        $this->assertSame('pending', $action['data']['status']);
    }

    public function test_performing_postgres_action()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/server-1/services/postgres/actions', [
            'form_params' => ['action' => 'restart'],
        ])->andReturn(
            new Response(202, [], '{"data": {"id": "action-4", "status": "pending"}}')
        );

        $action = $forge->performPostgresAction('org-123', 'server-1', ['action' => 'restart']);
        $this->assertSame('action-4', $action['data']['id']);
        $this->assertSame('pending', $action['data']['status']);
    }

    public function test_performing_redis_action()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/server-1/services/redis/actions', [
            'form_params' => ['action' => 'restart'],
        ])->andReturn(
            new Response(202, [], '{"data": {"id": "action-5", "status": "pending"}}')
        );

        $action = $forge->performRedisAction('org-123', 'server-1', ['action' => 'restart']);
        $this->assertSame('action-5', $action['data']['id']);
        $this->assertSame('pending', $action['data']['status']);
    }

    public function test_performing_mysql_action()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/server-1/services/mysql/actions', [
            'form_params' => ['action' => 'restart'],
        ])->andReturn(
            new Response(202, [], '{"data": {"id": "action-6", "status": "pending"}}')
        );

        $action = $forge->performMySQLAction('org-123', 'server-1', ['action' => 'restart']);
        $this->assertSame('action-6', $action['data']['id']);
        $this->assertSame('pending', $action['data']['status']);
    }

    public function test_performing_php_action()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/server-1/services/php/actions', [
            'form_params' => ['action' => 'restart'],
        ])->andReturn(
            new Response(202, [], '{"data": {"id": "action-7", "status": "pending"}}')
        );

        $action = $forge->performPHPAction('org-123', 'server-1', ['action' => 'restart']);
        $this->assertSame('action-7', $action['data']['id']);
        $this->assertSame('pending', $action['data']['status']);
    }

    public function test_performing_supervisor_action()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/server-1/services/supervisor/actions', [
            'form_params' => ['action' => 'restart'],
        ])->andReturn(
            new Response(202, [], '{"data": {"id": "action-8", "status": "pending"}}')
        );

        $action = $forge->performSupervisorAction('org-123', 'server-1', ['action' => 'restart']);
        $this->assertSame('action-8', $action['data']['id']);
        $this->assertSame('pending', $action['data']['status']);
    }
}
