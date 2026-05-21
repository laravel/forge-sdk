<?php

declare(strict_types=1);

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Laravel\Forge\Exceptions\FailedActionException;
use Laravel\Forge\Exceptions\ForbiddenException;
use Laravel\Forge\Exceptions\NotFoundException;
use Laravel\Forge\Exceptions\RateLimitExceededException;
use Laravel\Forge\Exceptions\TimeoutException;
use Laravel\Forge\Exceptions\ValidationException;
use Laravel\Forge\CursorPaginator;
use Laravel\Forge\Forge;
use Laravel\Forge\MakesHttpRequests;
use Laravel\Forge\Resources\ComposerCredential;
use Laravel\Forge\Resources\Database;
use Laravel\Forge\Resources\DatabaseUser;
use Laravel\Forge\Resources\DeployKey;
use Laravel\Forge\Resources\Deployment;
use Laravel\Forge\Resources\Domain;
use Laravel\Forge\Resources\FirewallRule;
use Laravel\Forge\Resources\Heartbeat;
use Laravel\Forge\Resources\Monitor;
use Laravel\Forge\Resources\NginxTemplate;
use Laravel\Forge\Resources\NpmCredential;
use Laravel\Forge\Resources\PHPVersion;
use Laravel\Forge\Resources\Recipe;
use Laravel\Forge\Resources\RecipeRun;
use Laravel\Forge\Resources\RedirectRule;
use Laravel\Forge\Resources\Role;
use Laravel\Forge\Resources\SecurityRule;
use Laravel\Forge\Resources\Server;
use Laravel\Forge\Resources\ServerCredential;
use Laravel\Forge\Resources\Site;
use Laravel\Forge\Resources\SSHKey;
use Laravel\Forge\Resources\StorageProvider;
use Laravel\Forge\Resources\Team;
use Laravel\Forge\Resources\TeamInvitation;
use Laravel\Forge\Resources\TeamMember;
use Laravel\Forge\Resources\User;
use Laravel\Forge\Resources\Webhook;
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
            new Response(200, [], '{"data": [{"id": 1, "name": "My Organization"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $organizations = $forge->organizations();
        $this->assertInstanceOf(CursorPaginator::class, $organizations);
        $this->assertCount(1, $organizations);
    }

    public function test_getting_single_organization()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "My Organization"}}')
        );

        $org = $forge->organization('org-123');
        $this->assertSame('1', $org->id);
    }

    public function test_getting_recipes_for_organization()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/recipes', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "name": "Recipe 1"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $this->assertCount(1, $forge->recipes('org-123'));
    }

    public function test_getting_servers_for_organization()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "name": "Server 1"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $servers = $forge->servers('org-123');
        $this->assertInstanceOf(CursorPaginator::class, $servers);
        $this->assertCount(1, $servers);
    }

    public function test_list_methods_forward_query_parameters()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers', ['query' => ['cursor' => 'abc', 'page' => ['size' => 5]]])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "name": "Server 1"}], "meta": {"next_cursor": null, "per_page": 5}}')
        );

        $servers = $forge->servers('org-123', ['cursor' => 'abc', 'page' => ['size' => 5]]);
        $this->assertInstanceOf(CursorPaginator::class, $servers);
        $this->assertCount(1, $servers);
    }

    public function test_getting_single_server()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "Production Server"}}')
        );

        $server = $forge->server('org-123', 1);
        $this->assertSame(1, $server->id);
    }

    public function test_creating_server()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers', [
            'json' => ['provider' => 'ocean2', 'size' => '1gb'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "provider": "ocean2", "isReady": false}}')
        );

        $server = $forge->createServer('org-123', ['provider' => 'ocean2', 'size' => '1gb'], false);
        $this->assertSame(1, $server->id);
    }

    public function test_deleting_server()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1', [])->andReturn(
            new Response(204)
        );

        $result = $forge->deleteServer('org-123', 1);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_updating_server()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/1', [
            'json' => ['name' => 'renamed-server', 'tags' => ['production', 'web']],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "renamed-server"}}')
        );

        $server = $forge->updateServer('org-123', 1, ['name' => 'renamed-server', 'tags' => ['production', 'web']]);
        $this->assertSame(1, $server->id);
        $this->assertSame('renamed-server', $server->name);
        $this->assertSame('org-123', $server->organizationSlug);
    }

    public function test_getting_server_network()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/network', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 2, "name": "Server 2"}, {"id": 3, "name": "Server 3"}]}')
        );

        $network = $forge->network('org-123', 1);
        $this->assertIsArray($network);
        $this->assertCount(2, $network);
        $this->assertInstanceOf(Server::class, $network[0]);
        $this->assertSame(2, $network[0]->id);
        $this->assertSame('org-123', $network[0]->organizationSlug);
    }

    public function test_updating_server_network()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/1/network', [
            'json' => ['servers' => [2, 3, 4]],
        ])->andReturn(
            new Response(202)
        );

        $forge->updateNetwork('org-123', 1, ['servers' => [2, 3, 4]]);
        $this->assertTrue(true);
    }

    public function test_getting_sites_for_server()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "name": "example.com"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $this->assertCount(1, $forge->serverSites('org-123', 1));
    }

    public function test_creating_site()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/sites', [
            'json' => ['domain' => 'example.com'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "domain": "example.com"}}')
        );

        $site = $forge->createSite('org-123', 1, ['domain' => 'example.com']);
        $this->assertSame(1, $site->id);
    }

    public function test_creating_balancer()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/sites/balancer', [
            'json' => ['method' => 'round_robin'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "balancer"}}')
        );

        $site = $forge->createBalancer('org-123', 1, ['method' => 'round_robin']);
        $this->assertSame(1, $site->id);
    }

    public function test_updating_site()
    {
        $this->expectNotToPerformAssertions();

        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/1/sites/1', [
            'json' => ['aliases' => ['foo.com']],
        ])->andReturn(
            new Response(202)
        );

        $forge->updateSite('org-123', 1, 1, ['aliases' => ['foo.com']]);
    }

    public function test_deleting_site()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/sites/1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteSite('org-123', 1, 1);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_heartbeats()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/heartbeats', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "name": "My Heartbeat", "interval": 60, "status": "active"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $heartbeats = $forge->heartbeats('org-123', 1, 1);
        $this->assertInstanceOf(CursorPaginator::class, $heartbeats);
        $this->assertCount(1, $heartbeats);
        $this->assertSame(1, $heartbeats[0]->id);
    }

    public function test_getting_single_heartbeat()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/heartbeats/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "My Heartbeat", "interval": 60, "status": "active"}}')
        );

        $heartbeat = $forge->heartbeat('org-123', 1, 1, 1);
        $this->assertSame(1, $heartbeat->id);
        $this->assertSame('My Heartbeat', $heartbeat->name);
    }

    public function test_creating_heartbeat()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/sites/1/heartbeats', [
            'json' => ['name' => 'My Heartbeat', 'frequency' => 60],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "My Heartbeat", "frequency": 60, "status": "active"}}')
        );

        $heartbeat = $forge->createHeartbeat('org-123', 1, 1, ['name' => 'My Heartbeat', 'frequency' => 60]);
        $this->assertSame(1, $heartbeat->id);
        $this->assertSame('My Heartbeat', $heartbeat->name);
        $this->assertSame(60, $heartbeat->frequency);
    }

    public function test_updating_heartbeat()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/1/sites/1/heartbeats/1', [
            'json' => ['name' => 'Updated Heartbeat'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "Updated Heartbeat", "interval": 60, "status": "active"}}')
        );

        $heartbeat = $forge->updateHeartbeat('org-123', 1, 1, 1, ['name' => 'Updated Heartbeat']);
        $this->assertSame('Updated Heartbeat', $heartbeat->name);
    }

    public function test_deleting_heartbeat()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/sites/1/heartbeats/1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteHeartbeat('org-123', 1, 1, 1);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_databases()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/database/schemas', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "name": "my_database"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $this->assertCount(1, $forge->databases('org-123', 1));
    }

    public function test_creating_database()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/database/schemas', [
            'json' => ['name' => 'my_database'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "my_database", "status": "installing"}}')
        );

        $database = $forge->createDatabase('org-123', 1, ['name' => 'my_database'], false);
        $this->assertSame(1, $database->id);
    }

    public function test_deleting_database()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/database/schemas/1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteDatabase('org-123', 1, 1);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_database_users()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/database/users', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "name": "db_user"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $this->assertCount(1, $forge->databaseUsers('org-123', 1));
    }

    public function test_creating_database_user()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/database/users', [
            'json' => ['name' => 'db_user', 'password' => 'secret'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "db_user", "status": "installing"}}')
        );

        $user = $forge->createDatabaseUser('org-123', 1, ['name' => 'db_user', 'password' => 'secret'], false);
        $this->assertSame(1, $user->id);
    }

    public function test_updating_database_user()
    {
        $this->expectNotToPerformAssertions();

        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/1/database/users/1', [
            'json' => ['databases' => [1]],
        ])->andReturn(
            new Response(202)
        );

        $forge->updateDatabaseUser('org-123', 1, 1, ['databases' => [1]]);
    }

    public function test_getting_background_processes()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/background-processes', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "name": "My Process"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $this->assertCount(1, $forge->backgroundProcesses('org-123', 1));
    }

    public function test_creating_background_process()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/background-processes', [
            'json' => ['command' => 'node server.js'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "command": "node server.js"}}')
        );

        $process = $forge->createBackgroundProcess('org-123', 1, ['command' => 'node server.js']);
        $this->assertSame(1, $process->id);
    }

    public function test_updating_background_process()
    {
        $this->expectNotToPerformAssertions();

        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/1/background-processes/1', [
            'json' => ['processes' => 2],
        ])->andReturn(
            new Response(202)
        );

        $forge->updateBackgroundProcess('org-123', 1, 1, ['processes' => 2]);
    }

    public function test_deleting_background_process()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/background-processes/1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteBackgroundProcess('org-123', 1, 1);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_teams()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/teams', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "name": "Development Team"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $teams = $forge->teams('org-123');
        $this->assertInstanceOf(CursorPaginator::class, $teams);
        $this->assertCount(1, $teams);
    }

    public function test_getting_single_team()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/teams/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "Development Team"}}')
        );

        $team = $forge->team('org-123', 1);
        $this->assertSame(1, $team->id);
    }

    public function test_creating_team()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/teams', [
            'json' => ['name' => 'New Team'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 2, "name": "New Team"}}')
        );

        $team = $forge->createTeam('org-123', ['name' => 'New Team']);
        $this->assertSame(2, $team->id);
    }

    public function test_updating_team()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/teams/1', [
            'json' => ['name' => 'Updated Team'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "Updated Team"}}')
        );

        $team = $forge->updateTeam('org-123', 1, ['name' => 'Updated Team']);
        $this->assertSame('Updated Team', $team->name);
    }

    public function test_deleting_team()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/teams/1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteTeam('org-123', 1);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_team_members()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/teams/1/members', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "name": "John Doe"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $this->assertCount(1, $forge->teamMembers('org-123', 1));
    }

    public function test_getting_team_invitations()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/teams/1/invites', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "email": "user@example.com"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $this->assertCount(1, $forge->teamInvitations('org-123', 1));
    }

    public function test_getting_single_team_invitation()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/teams/1/invites/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "email": "user@example.com"}}')
        );

        $invitation = $forge->teamInvitation('org-123', 1, 1);
        $this->assertSame(1, $invitation->id);
    }

    public function test_creating_team_invitation()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/teams/1/invites', [
            'json' => ['email' => 'newuser@example.com', 'role_id' => 1],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 2, "email": "newuser@example.com"}}')
        );

        $invitation = $forge->createTeamInvitation('org-123', 1, ['email' => 'newuser@example.com', 'role_id' => 1]);
        $this->assertSame(2, $invitation->id);
    }

    public function test_deleting_team_invitation()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/teams/1/invites/1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteTeamInvitation('org-123', 1, 1);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_team_servers()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/teams/1/servers', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "name": "Production Server"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $this->assertCount(1, $forge->teamServers('org-123', 1));
    }

    public function test_creating_team_server_share()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/teams/1/servers', [
            'json' => ['server_id' => 1],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "server_id": 1}}')
        );

        $share = $forge->createTeamServersShare('org-123', 1, ['server_id' => 1]);
        $this->assertSame(1, $share->id);
    }

    public function test_deleting_team_server_share()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/teams/1/servers/1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteTeamServersShare('org-123', 1, 1);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_team_server_credentials()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/teams/1/server-credentials', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "name": "AWS Credentials"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $this->assertCount(1, $forge->teamServerCredentials('org-123', 1));
    }

    public function test_sharing_server_credential()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/teams/1/server-credentials', [
            'json' => ['credential_id' => 1],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 2, "credential_id": 1}}')
        );

        $share = $forge->createTeamServerCredentialsShare('org-123', 1, ['credential_id' => 1]);
        $this->assertSame(2, $share->id);
    }

    public function test_getting_roles()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/roles', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "name": "Admin"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $this->assertCount(1, $forge->roles('org-123'));
    }

    public function test_getting_single_role()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/roles/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "Admin"}}')
        );

        $role = $forge->role('org-123', 1);
        $this->assertSame(1, $role->id);
    }

    public function test_creating_role()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/roles', [
            'json' => ['name' => 'Developer'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 2, "name": "Developer"}}')
        );

        $role = $forge->createRole('org-123', ['name' => 'Developer']);
        $this->assertSame(2, $role->id);
    }

    public function test_updating_role()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/roles/1', [
            'json' => ['name' => 'Super Admin'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "Super Admin"}}')
        );

        $role = $forge->updateRole('org-123', 1, ['name' => 'Super Admin']);
        $this->assertSame('Super Admin', $role->name);
    }

    public function test_deleting_role()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/roles/1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteRole('org-123', 1);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_predefined_roles()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'predefined-roles', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "name": "Administrator"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $this->assertCount(1, $forge->predefinedRoles());
    }

    public function test_getting_permissions()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'permissions', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "name": "manage_servers"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $this->assertCount(1, $forge->permissions());
    }

    public function test_getting_providers()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'providers', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "name": "Digital Ocean"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $this->assertCount(1, $forge->providers());
    }

    public function test_getting_single_provider()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'providers/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "Digital Ocean"}}')
        );

        $provider = $forge->provider(1);
        $this->assertSame(1, $provider->id);
    }

    public function test_getting_provider_regions()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'providers/1/regions', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "name": "New York 3"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $this->assertCount(1, $forge->providerRegions(1));
    }

    public function test_getting_provider_sizes()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'providers/1/sizes', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "name": "1GB"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $this->assertCount(1, $forge->providerSizes(1));
    }

    public function test_getting_horizon_integration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/integrations/horizon', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "status": "installed"}}')
        );

        $integration = $forge->getHorizon('org-123', 1, 1);
        $this->assertSame(1, $integration->id);
    }

    public function test_creating_horizon_integration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/sites/1/integrations/horizon', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "type": "horizon"}}')
        );

        $integration = $forge->createHorizon('org-123', 1, 1);
        $this->assertSame(1, $integration->id);
    }

    public function test_deleting_horizon_integration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/sites/1/integrations/horizon', [])->andReturn(
            new Response(204)
        );

        $forge->deleteHorizon('org-123', 1, 1);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_creating_octane_integration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/sites/1/integrations/octane', [
            'json' => ['server' => 'roadrunner'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 2, "type": "octane"}}')
        );

        $integration = $forge->createOctane('org-123', 1, 1, ['server' => 'roadrunner']);
        $this->assertSame(2, $integration->id);
    }

    public function test_getting_reverb_integration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/integrations/reverb', [])->andReturn(
            new Response(200, [], '{"data": {"id": 3, "status": "installed"}}')
        );

        $integration = $forge->getReverb('org-123', 1, 1);
        $this->assertSame(3, $integration->id);
    }

    public function test_creating_reverb_integration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/sites/1/integrations/reverb', [])->andReturn(
            new Response(200, [], '{"data": {"id": 3, "type": "reverb"}}')
        );

        $integration = $forge->createReverb('org-123', 1, 1);
        $this->assertSame(3, $integration->id);
    }

    public function test_deleting_reverb_integration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/sites/1/integrations/reverb', [])->andReturn(
            new Response(204)
        );

        $forge->deleteReverb('org-123', 1, 1);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_inertia_integration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/integrations/inertia', [])->andReturn(
            new Response(200, [], '{"data": {"id": 4, "status": "installed"}}')
        );

        $integration = $forge->getInertia('org-123', 1, 1);
        $this->assertSame(4, $integration->id);
    }

    public function test_creating_inertia_integration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/sites/1/integrations/inertia', [])->andReturn(
            new Response(200, [], '{"data": {"id": 4, "type": "inertia"}}')
        );

        $integration = $forge->createInertia('org-123', 1, 1);
        $this->assertSame(4, $integration->id);
    }

    public function test_getting_pulse_integration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/integrations/pulse', [])->andReturn(
            new Response(200, [], '{"data": {"id": 5, "status": "installed"}}')
        );

        $integration = $forge->getPulse('org-123', 1, 1);
        $this->assertSame(5, $integration->id);
    }

    public function test_creating_pulse_integration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/sites/1/integrations/pulse', [])->andReturn(
            new Response(200, [], '{"data": {"id": 5, "type": "pulse"}}')
        );

        $integration = $forge->createPulse('org-123', 1, 1);
        $this->assertSame(5, $integration->id);
    }

    public function test_deleting_pulse_integration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/sites/1/integrations/pulse', [])->andReturn(
            new Response(204)
        );

        $forge->deletePulse('org-123', 1, 1);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_maintenance_integration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/integrations/laravel-maintenance', [])->andReturn(
            new Response(200, [], '{"data": {"id": 6, "status": "installed"}}')
        );

        $integration = $forge->getMaintenance('org-123', 1, 1);
        $this->assertSame(6, $integration->id);
    }

    public function test_creating_maintenance_integration()
    {
        $this->expectNotToPerformAssertions();

        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/sites/1/integrations/laravel-maintenance', [])->andReturn(
            new Response(202)
        );

        $forge->createMaintenance('org-123', 1, 1);
    }

    public function test_deleting_maintenance_integration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/sites/1/integrations/laravel-maintenance', [])->andReturn(
            new Response(204)
        );

        $forge->deleteMaintenance('org-123', 1, 1);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_scheduler_integration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/integrations/laravel-scheduler', [])->andReturn(
            new Response(200, [], '{"data": {"id": 7, "status": "installed"}}')
        );

        $integration = $forge->getScheduler('org-123', 1, 1);
        $this->assertSame(7, $integration->id);
    }

    public function test_creating_scheduler_integration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/sites/1/integrations/laravel-scheduler', [])->andReturn(
            new Response(200, [], '{"data": {"id": 7, "type": "laravel-scheduler"}}')
        );

        $integration = $forge->createScheduler('org-123', 1, 1);
        $this->assertSame(7, $integration->id);
    }

    public function test_deleting_scheduler_integration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/sites/1/integrations/laravel-scheduler', [])->andReturn(
            new Response(204)
        );

        $forge->deleteScheduler('org-123', 1, 1);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_updating_domain_with_patch()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PATCH', 'orgs/org-123/servers/1/sites/1/domains/1', [
            'json' => ['allow_wildcard_subdomains' => true],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "allow_wildcard_subdomains": true}}')
        );

        $domain = $forge->updateDomain('org-123', 1, 1, 1, ['allow_wildcard_subdomains' => true]);
        $this->assertTrue($domain->allowWildcardSubdomains);
    }

    public function test_getting_domains()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/domains', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "name": "example.com"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $domains = $forge->domains('org-123', 1, 1);
        $this->assertInstanceOf(CursorPaginator::class, $domains);
        $this->assertCount(1, $domains);
    }

    public function test_creating_domain()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/sites/1/domains', [
            'json' => ['name' => 'api.example.com'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 2, "name": "api.example.com"}}')
        );

        $domain = $forge->createDomain('org-123', 1, 1, ['name' => 'api.example.com']);
        $this->assertSame(2, $domain->id);
    }

    public function test_deleting_domain()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/sites/1/domains/1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteDomain('org-123', 1, 1, 1);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_domain_configurations()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/domains/1/configurations', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "type": "nginx"}]}')
        );

        $this->assertCount(1, $forge->domainConfigurations('org-123', 1, 1, 1));
    }

    public function test_creating_domain_action()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/sites/1/domains/1/actions', [
            'json' => ['action' => 'verify'],
        ])->andReturn(
            new Response(202, [], '{"data": {"id": 1, "status": "pending"}}')
        );

        $action = $forge->createDomainAction('org-123', 1, 1, 1, ['action' => 'verify']);
        $this->assertSame(1, $action['data']['id']);
    }

    // Deployments - Webhooks (4 tests)

    public function test_getting_webhooks()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/123/sites/456/webhooks', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "url": "https://example.com/webhook"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $this->assertCount(1, $forge->webhooks('org-123', 123, 456));
    }

    public function test_getting_single_webhook()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/123/sites/456/webhooks/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "url": "https://example.com/webhook"}}')
        );

        $webhook = $forge->webhook('org-123', 123, 456, 1);
        $this->assertSame(1, $webhook->id);
    }

    public function test_creating_webhook()
    {
        $this->expectNotToPerformAssertions();

        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/123/sites/456/webhooks', [
            'json' => ['url' => 'https://example.com/webhook'],
        ])->andReturn(
            new Response(202)
        );

        $forge->createWebhook('org-123', 123, 456, ['url' => 'https://example.com/webhook']);
    }

    public function test_deleting_webhook()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/123/sites/456/webhooks/1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteWebhook('org-123', 123, 456, 1);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    // Deployments - Main (3 tests)

    public function test_getting_deployments()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/123/sites/456/deployments', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "status": "finished"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $deployments = $forge->deployments('org-123', 123, 456);
        $this->assertInstanceOf(CursorPaginator::class, $deployments);
        $this->assertCount(1, $deployments);
    }

    public function test_getting_single_deployment()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/123/sites/456/deployments/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "status": "finished"}}')
        );

        $deployment = $forge->deployment('org-123', 123, 456, 1);
        $this->assertSame(1, $deployment->id);
    }

    public function test_creating_deployment()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/123/sites/456/deployments', [])->andReturn(
            new Response(200, [], '{"data": {"id": 2, "status": "pending"}}')
        );

        $deployment = $forge->createDeployment('org-123', 123, 456);
        $this->assertSame(2, $deployment->id);
    }

    // Deployment Status (2 tests)

    public function test_getting_deployment_status()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/123/sites/456/deployments/status', [])->andReturn(
            new Response(200, [], '{"data": {"is_deploying": false, "last_deployment_status": "finished"}}')
        );

        $status = $forge->deploymentStatus('org-123', 123, 456);
        $this->assertFalse($status['is_deploying']);
    }

    public function test_updating_deployment_state()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/123/sites/456/deployments/status', [])->andReturn(
            new Response(204)
        );

        $forge->disableQuickDeploy('org-123', 123, 456);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    // Deployment Script (2 tests)

    public function test_getting_deployment_script()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/123/sites/456/deployments/script', [])->andReturn(
            new Response(200, [], '{"data": {"type": "deployment-scripts", "id": "1", "attributes": {"content": "cd /home/forge/example.com\ngit pull origin main"}}}')
        );

        $script = $forge->deploymentScript('org-123', 123, 456);
        $this->assertStringContainsString('git pull', $script);
    }

    public function test_updating_deployment_script()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/123/sites/456/deployments/script', [
            'json' => ['script' => 'cd /home/forge/example.com\ngit pull origin main\nphp artisan migrate'],
        ])->andReturn(
            new Response(200, [], '{"data": {"type": "deploymentScripts", "id": "1", "attributes": {"content": "cd /home/forge/example.com", "auto_source": false}, "links": {"self": "https://forge.laravel.com"}}}')
        );

        $script = $forge->updateDeploymentScript('org-123', 123, 456, ['script' => 'cd /home/forge/example.com\ngit pull origin main\nphp artisan migrate']);
        $this->assertIsString($script);
        $this->assertSame('cd /home/forge/example.com', $script);
    }

    // Deployment Trigger (2 tests)

    public function test_getting_deployment_trigger_url()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/123/sites/456/deployments/deploy-hook', [])->andReturn(
            new Response(200, [], '{"data": {"type": "deploy-hooks", "id": "1", "attributes": {"url": "https://forge.laravel.com/servers/123/sites/456/deploy/http?token=abc123"}}}')
        );

        $url = $forge->deploymentTriggerUrl('org-123', 123, 456);
        $this->assertStringContainsString('deploy/http', $url);
    }

    public function test_updating_deployment_trigger_url()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/123/sites/456/deployments/deploy-hook', [
            'json' => ['regenerate' => true],
        ])->andReturn(
            new Response(200, [], '{"data": {"type": "deploymentHooks", "id": "1", "attributes": {"url": "https://forge.laravel.com/servers/123/sites/456/deploy/http?token=newtoken"}, "links": {"self": "https://forge.laravel.com"}}}')
        );

        $url = $forge->updateDeploymentTriggerUrl('org-123', 123, 456, ['regenerate' => true]);
        $this->assertIsString($url);
        $this->assertStringContainsString('deploy/http', $url);
    }

    // Push to Deploy (2 tests)

    public function test_creating_push_to_deploy()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/123/sites/456/deployments/push-to-deploy', [
            'json' => ['provider' => 'github', 'repository' => 'user/repo'],
        ])->andReturn(
            new Response(204)
        );

        $forge->enablePushToDeploy('org-123', 123, 456, ['provider' => 'github', 'repository' => 'user/repo']);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_deleting_push_to_deploy()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/123/sites/456/deployments/push-to-deploy', [])->andReturn(
            new Response(204)
        );

        $forge->disablePushToDeploy('org-123', 123, 456);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    // Deployment Log (1 test)

    public function test_getting_deployment_log()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/123/sites/456/deployments/1/log', [])->andReturn(
            new Response(200, [], '{"data": {"type": "deployment-outputs", "id": "1", "attributes": {"output": "Cloning repository...\nInstalling dependencies...\nDeployment finished successfully."}}}')
        );

        $log = $forge->deploymentLog('org-123', 123, 456, 1);
        $this->assertStringContainsString('Deployment finished successfully', $log);
    }

    // Deploy Keys (3 tests)

    public function test_getting_deploy_key()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/deploy-key', [])->andReturn(
            new Response(200, [], '{"data": {"type": "deploy-keys", "id": "1", "attributes": {"key": "ssh-rsa AAAAB3NzaC1yc2EAAAA forge@example.com"}}}')
        );

        $deployKey = $forge->deployKey('org-123', 1, 1);
        $this->assertInstanceOf(DeployKey::class, $deployKey);
        $this->assertStringStartsWith('ssh-rsa', $deployKey->key);
    }

    public function test_creating_deploy_key()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/sites/1/deploy-key', [])->andReturn(
            new Response(200, [], '{"data": {"type": "deploy-keys", "id": "1", "attributes": {"key": "ssh-rsa AAAAB3NzaC1yc2EAAAA forge@example.com"}}}')
        );

        $deployKey = $forge->createDeployKey('org-123', 1, 1);
        $this->assertInstanceOf(DeployKey::class, $deployKey);
        $this->assertNotEmpty($deployKey->key);
    }

    public function test_deleting_deploy_key()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/sites/1/deploy-key', [])->andReturn(
            new Response(204)
        );

        $forge->deleteDeployKey('org-123', 1, 1);
        $this->assertTrue(true);
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

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/999', [])->andReturn(
            new Response(404)
        );

        $forge->server('org-123', 999);
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
            'json' => ['invalid' => 'data'],
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
            new Response(200, [], '{"data": [{"id": 1, "name": "AWS Credentials"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $this->assertCount(1, $forge->serverCredentials('org-123'));
    }

    public function test_getting_single_server_credential()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/server-credentials/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "AWS Credentials"}}')
        );

        $credential = $forge->serverCredential('org-123', 1);
        $this->assertSame(1, $credential->id);
    }

    public function test_getting_vpcs()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/server-credentials/1/regions/us-east-1/vpcs', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "name": "Production VPC"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $this->assertCount(1, $forge->vpcs('org-123', 1, 'us-east-1'));
    }

    public function test_getting_single_vpc()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/server-credentials/1/regions/us-east-1/vpcs/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "Production VPC"}}')
        );

        $vpc = $forge->vpc('org-123', 1, 'us-east-1', 1);
        $this->assertSame(1, $vpc->id);
    }

    public function test_creating_vpc()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/server-credentials/1/regions/us-east-1/vpcs', [
            'json' => ['name' => 'New VPC', 'cidr_block' => '10.0.0.0/16'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 2, "name": "New VPC"}}')
        );

        $vpc = $forge->createVpc('org-123', 1, 'us-east-1', ['name' => 'New VPC', 'cidr_block' => '10.0.0.0/16']);
        $this->assertSame(2, $vpc->id);
    }

    public function test_getting_forge_recipes()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'forge-recipes', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "name": "Install Node.js"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $this->assertCount(1, $forge->forgeRecipes());
    }

    public function test_getting_single_forge_recipe()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'forge-recipes/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "Install Node.js"}}')
        );

        $recipe = $forge->forgeRecipe(1);
        $this->assertSame(1, $recipe->id);
    }

    public function test_creating_recipe()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/recipes', [
            'json' => ['name' => 'My Recipe', 'script' => 'echo "Hello"'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "My Recipe"}}')
        );

        $recipe = $forge->createRecipe('org-123', ['name' => 'My Recipe', 'script' => 'echo "Hello"']);
        $this->assertSame(1, $recipe->id);
    }

    public function test_updating_recipe()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/recipes/1', [
            'json' => ['name' => 'Updated Recipe'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "Updated Recipe"}}')
        );

        $recipe = $forge->updateRecipe('org-123', 1, ['name' => 'Updated Recipe']);
        $this->assertSame('Updated Recipe', $recipe->name);
    }

    public function test_deleting_recipe()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/recipes/1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteRecipe('org-123', 1);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_recipe_runs()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/recipes/1/runs', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "status": "completed"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $this->assertCount(1, $forge->recipeRuns('org-123', 1));
    }

    public function test_getting_single_recipe_run()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/recipes/1/runs/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "status": "completed"}}')
        );

        $run = $forge->recipeRun('org-123', 1, 1);
        $this->assertSame(1, $run->id);
    }

    public function test_creating_recipe_run()
    {
        $this->expectNotToPerformAssertions();

        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/recipes/1/runs', [
            'json' => ['server_id' => 1],
        ])->andReturn(
            new Response(202)
        );

        $forge->createRecipeRun('org-123', 1, ['server_id' => 1]);
    }

    public function test_getting_server_events()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/events', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "description": "Server created"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $this->assertCount(1, $forge->serverEvents('org-123', 1));
    }

    public function test_getting_single_server_event()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/events/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "description": "Server created"}}')
        );

        $event = $forge->serverEvent('org-123', 1, 1);
        $this->assertSame(1, $event->id);
    }

    public function test_getting_archived_servers()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/archives', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 2, "name": "Archived Server"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $this->assertCount(1, $forge->archivedServers('org-123'));
    }

    public function test_creating_archived_server()
    {
        $this->expectNotToPerformAssertions();

        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/archives', [
            'json' => ['server_id' => 1],
        ])->andReturn(
            new Response(202)
        );

        $forge->createArchivedServer('org-123', ['server_id' => 1]);
    }

    public function test_deleting_archived_server()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/archives/1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteArchivedServer('org-123', 1);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_php_versions()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/php/versions', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "version": "8.3"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $this->assertCount(1, $forge->phpVersions('org-123', 1));
    }

    public function test_installing_php_version()
    {
        $this->expectNotToPerformAssertions();

        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/php/versions', [
            'json' => ['version' => '8.3'],
        ])->andReturn(
            new Response(202)
        );

        $forge->installPhpVersion('org-123', 1, ['version' => '8.3']);
    }

    public function test_updating_php_version()
    {
        $this->expectNotToPerformAssertions();

        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/1/php/versions/1', [
            'json' => ['version' => '8.3'],
        ])->andReturn(
            new Response(202)
        );

        $forge->updatePhpVersion('org-123', 1, 1, ['version' => '8.3']);
    }

    public function test_deleting_php_version()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/php/versions/1', [])->andReturn(
            new Response(204)
        );

        $forge->deletePhpVersion('org-123', 1, 1);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_php_cli_version()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/php/cli-version', [])->andReturn(
            new Response(200, [], '{"data": {"version": "php83", "displayVersion": "PHP 8.3"}}')
        );

        $cliVersion = $forge->phpCliVersion('org-123', 1);
        $this->assertIsArray($cliVersion);
        $this->assertSame('php83', $cliVersion['version']);
        $this->assertSame('PHP 8.3', $cliVersion['displayVersion']);
    }

    public function test_updating_php_cli_version()
    {
        $this->expectNotToPerformAssertions();

        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/1/php/cli-version', [
            'json' => ['version' => 'php84'],
        ])->andReturn(
            new Response(204)
        );

        $forge->updatePhpCliVersion('org-123', 1, ['version' => 'php84']);
    }

    public function test_getting_php_site_version()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/php/site-version', [])->andReturn(
            new Response(200, [], '{"data": {"version": "php83", "displayVersion": "PHP 8.3"}}')
        );

        $siteVersion = $forge->phpSiteVersion('org-123', 1);
        $this->assertIsArray($siteVersion);
        $this->assertSame('php83', $siteVersion['version']);
        $this->assertSame('PHP 8.3', $siteVersion['displayVersion']);
    }

    public function test_updating_php_site_version()
    {
        $this->expectNotToPerformAssertions();

        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/1/php/site-version', [
            'json' => ['version' => 'php84'],
        ])->andReturn(
            new Response(204)
        );

        $forge->updatePhpSiteVersion('org-123', 1, ['version' => 'php84']);
    }

    public function test_getting_php_fpm_config()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/php/versions/83/configs/fpm', [])->andReturn(
            new Response(200, [], '{"data": {"type": "php-fpm-configs", "id": "1", "attributes": {"configuration": "pm = dynamic"}}}')
        );

        $config = $forge->phpFpm('org-123', 1, 83);
        $this->assertIsString($config);
        $this->assertStringContainsString('pm = dynamic', $config);
    }

    public function test_updating_php_fpm_config()
    {
        $this->expectNotToPerformAssertions();

        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/1/php/versions/83/configs/fpm', [
            'json' => ['content' => 'pm = ondemand'],
        ])->andReturn(
            new Response(202)
        );

        $forge->updatePhpFpm('org-123', 1, 83, ['content' => 'pm = ondemand']);
    }

    public function test_getting_php_cli_config()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/php/versions/83/configs/cli', [])->andReturn(
            new Response(200, [], '{"data": {"type": "php-cli-configs", "id": "1", "attributes": {"configuration": "memory_limit = 256M"}}}')
        );

        $config = $forge->phpCli('org-123', 1, 83);
        $this->assertIsString($config);
        $this->assertStringContainsString('memory_limit = 256M', $config);
    }

    public function test_updating_php_cli_config()
    {
        $this->expectNotToPerformAssertions();

        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/1/php/versions/83/configs/cli', [
            'json' => ['content' => 'memory_limit = 512M'],
        ])->andReturn(
            new Response(202)
        );

        $forge->updatePhpCli('org-123', 1, 83, ['content' => 'memory_limit = 512M']);
    }

    public function test_getting_php_pool_config()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/php/versions/83/configs/pool', [])->andReturn(
            new Response(200, [], '{"data": {"type": "php-pool-configs", "id": "1", "attributes": {"configuration": "pm.max_children = 50"}}}')
        );

        $config = $forge->phpPool('org-123', 1, 83);
        $this->assertIsString($config);
        $this->assertStringContainsString('pm.max_children = 50', $config);
    }

    public function test_updating_php_pool_config()
    {
        $this->expectNotToPerformAssertions();

        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/1/php/versions/83/configs/pool', [
            'json' => ['content' => 'pm.max_children = 100'],
        ])->andReturn(
            new Response(202)
        );

        $forge->updatePhpPool('org-123', 1, 83, ['content' => 'pm.max_children = 100']);
    }

    public function test_getting_php_max_upload_size()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/php/max-upload-size', [])->andReturn(
            new Response(200, [], '{"data": {"size": "256M"}}')
        );

        $uploadSize = $forge->phpMaxUploadSize('org-123', 1);
        $this->assertIsArray($uploadSize);
        $this->assertSame('256M', $uploadSize['size']);
    }

    public function test_updating_php_max_upload_size()
    {
        $this->expectNotToPerformAssertions();

        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/1/php/max-upload-size', [
            'json' => ['size' => '512M'],
        ])->andReturn(
            new Response(202)
        );

        $forge->updatePhpMaxUploadSize('org-123', 1, ['size' => '512M']);
    }

    public function test_getting_php_max_execution_time()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/php/max-execution-time', [])->andReturn(
            new Response(200, [], '{"data": {"time": "60"}}')
        );

        $executionTime = $forge->phpMaxExecutionTime('org-123', 1);
        $this->assertIsArray($executionTime);
        $this->assertSame('60', $executionTime['time']);
    }

    public function test_updating_php_max_execution_time()
    {
        $this->expectNotToPerformAssertions();

        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/1/php/max-execution-time', [
            'json' => ['time' => '120'],
        ])->andReturn(
            new Response(202)
        );

        $forge->updatePhpMaxExecutionTime('org-123', 1, ['time' => '120']);
    }

    public function test_getting_php_opcache()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/php/opcache', [])->andReturn(
            new Response(200, [], '{"data": {"status": "enabled", "memory": "128"}}')
        );

        $opcache = $forge->phpOpcache('org-123', 1);
        $this->assertIsArray($opcache);
        $this->assertSame('enabled', $opcache['status']);
        $this->assertSame('128', $opcache['memory']);
    }

    public function test_creating_php_opcache()
    {
        $this->expectNotToPerformAssertions();

        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/php/opcache', [
            'json' => ['memory' => '256'],
        ])->andReturn(
            new Response(202)
        );

        $forge->createPhpOpcache('org-123', 1, ['memory' => '256']);
    }

    public function test_deleting_php_opcache()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/php/opcache', [])->andReturn(
            new Response(204)
        );

        $forge->deletePhpOpcache('org-123', 1);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_organization_sites()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/sites', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "name": "example.com"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $this->assertCount(1, $forge->organizationSites('org-123'));
    }

    public function test_getting_organization_site()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/sites/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "example.com"}}')
        );

        $site = $forge->organizationSite('org-123', 1);
        $this->assertSame(1, $site->id);
    }

    // Scheduled Jobs (5 tests)

    public function test_getting_scheduled_jobs()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/scheduled-jobs', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "command": "php artisan schedule:run", "frequency": "hourly"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $scheduledJobs = $forge->scheduledJobs('org-123', 1);
        $this->assertInstanceOf(CursorPaginator::class, $scheduledJobs);
        $this->assertCount(1, $scheduledJobs);
    }

    public function test_getting_single_scheduled_job()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/scheduled-jobs/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "command": "php artisan schedule:run", "frequency": "hourly"}}')
        );

        $job = $forge->scheduledJob('org-123', 1, 1);
        $this->assertSame(1, $job->id);
    }

    public function test_creating_scheduled_job()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/scheduled-jobs', [
            'json' => ['command' => 'php artisan queue:work', 'frequency' => 'daily', 'user' => 'forge'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 2, "command": "php artisan queue:work", "frequency": "daily", "user": "forge"}}')
        );

        $job = $forge->createScheduledJob('org-123', 1, ['command' => 'php artisan queue:work', 'frequency' => 'daily', 'user' => 'forge']);
        $this->assertSame(2, $job->id);
    }

    public function test_deleting_scheduled_job()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/scheduled-jobs/1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteScheduledJob('org-123', 1, 1);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_scheduled_job_output()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/scheduled-jobs/1/output', [])->andReturn(
            new Response(200, [], '{"data": {"type": "job-outputs", "id": "1", "attributes": {"output": "Job started at 2025-11-18 10:00:00\nProcessing items...\nJob completed successfully."}}}')
        );

        $output = $forge->scheduledJobOutput('org-123', 1, 1);
        $this->assertStringContainsString('Job completed successfully', $output);
    }

    // Firewall Rules (4 tests)

    public function test_getting_firewall_rules()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/firewall-rules', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "name": "SSH Access", "port": "22"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $firewallRules = $forge->firewallRules('org-123', 1);
        $this->assertInstanceOf(CursorPaginator::class, $firewallRules);
        $this->assertCount(1, $firewallRules);
    }

    public function test_getting_single_firewall_rule()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/firewall-rules/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "SSH Access", "port": "22"}}')
        );

        $rule = $forge->firewallRule('org-123', 1, 1);
        $this->assertSame(1, $rule->id);
    }

    public function test_creating_firewall_rule()
    {
        $this->expectNotToPerformAssertions();

        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/firewall-rules', [
            'json' => ['name' => 'HTTP Access', 'port' => '80'],
        ])->andReturn(
            new Response(202)
        );

        $forge->createFirewallRule('org-123', 1, ['name' => 'HTTP Access', 'port' => '80']);
    }

    public function test_deleting_firewall_rule()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/firewall-rules/1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteFirewallRule('org-123', 1, 1);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    // Monitors (4 tests)

    public function test_getting_monitors()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/monitors', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "type": "cpu", "threshold": "80"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $this->assertCount(1, $forge->monitors('org-123', 1));
    }

    public function test_getting_single_monitor()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/monitors/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "type": "cpu", "threshold": "80"}}')
        );

        $monitor = $forge->monitor('org-123', 1, 1);
        $this->assertSame(1, $monitor->id);
    }

    public function test_creating_monitor()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/monitors', [
            'json' => ['type' => 'disk', 'threshold' => '90'],
        ])->andReturn(
            new Response(202, [], '{"data": {"id": 1, "type": "disk", "threshold": "90"}}')
        );

        $monitor = $forge->createMonitor('org-123', 1, ['type' => 'disk', 'threshold' => '90']);
        $this->assertSame(1, $monitor->id);
    }

    public function test_deleting_monitor()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/monitors/1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteMonitor('org-123', 1, 1);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    // SSH Keys (6 tests)

    public function test_getting_ssh_keys()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/ssh-keys', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "name": "Deploy Key", "username": "forge"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $this->assertCount(1, $forge->sshKeys('org-123', 1));
    }

    public function test_getting_single_ssh_key()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/ssh-keys/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "Deploy Key", "username": "forge"}}')
        );

        $key = $forge->sshKey('org-123', 1, 1);
        $this->assertSame(1, $key->id);
    }

    public function test_creating_ssh_key()
    {
        $this->expectNotToPerformAssertions();

        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/ssh-keys', [
            'json' => ['name' => 'Production Key', 'key' => 'ssh-rsa AAAAB3...'],
        ])->andReturn(
            new Response(202)
        );

        $forge->createSSHKey('org-123', 1, ['name' => 'Production Key', 'key' => 'ssh-rsa AAAAB3...']);
    }

    public function test_deleting_ssh_key()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/ssh-keys/1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteSSHKey('org-123', 1, 1);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_server_public_key()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/key', [])->andReturn(
            new Response(200, [], '{"data": {"type": "server-keys", "id": "1", "attributes": {"public_key": "ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQC..."}}}')
        );

        $publicKey = $forge->serverKey('org-123', 1);
        $this->assertStringContainsString('ssh-rsa', $publicKey);
    }

    public function test_updating_server_public_key()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/1/key', [
            'json' => ['public_key' => 'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQD...'],
        ])->andReturn(
            new Response(200, [], '{"data": {"type": "server-keys", "id": "1", "attributes": {"public_key": "ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQD..."}}}')
        );

        $publicKey = $forge->updateServerKey('org-123', 1, ['public_key' => 'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQD...']);
        $this->assertStringContainsString('ssh-rsa', $publicKey);
    }

    // Nginx Templates (5 tests)

    public function test_getting_nginx_templates()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/nginx/templates', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "name": "Laravel Template", "content": "server { listen 80; }"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $this->assertCount(1, $forge->nginxTemplates('org-123', 1));
    }

    public function test_getting_single_nginx_template()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/nginx/templates/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "Laravel Template", "content": "server { listen 80; }"}}')
        );

        $template = $forge->nginxTemplate('org-123', 1, 1);
        $this->assertSame(1, $template->id);
    }

    public function test_creating_nginx_template()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/nginx/templates', [
            'json' => ['name' => 'Custom Template', 'content' => 'server { listen 443 ssl; }'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 2, "name": "Custom Template", "content": "server { listen 443 ssl; }"}}')
        );

        $template = $forge->createNginxTemplate('org-123', 1, ['name' => 'Custom Template', 'content' => 'server { listen 443 ssl; }']);
        $this->assertSame(2, $template->id);
    }

    public function test_updating_nginx_template()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/1/nginx/templates/1', [
            'json' => ['content' => 'server { listen 8080; }'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "Laravel Template", "content": "server { listen 8080; }"}}')
        );

        $template = $forge->updateNginxTemplate('org-123', 1, 1, ['content' => 'server { listen 8080; }']);
        $this->assertSame('server { listen 8080; }', $template->content);
    }

    public function test_deleting_nginx_template()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/nginx/templates/1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteNginxTemplate('org-123', 1, 1);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    // Security Rules (5 tests)

    public function test_getting_security_rules()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/security-rules', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "name": "Block Bad Bots", "path": "/admin"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $this->assertCount(1, $forge->securityRules('org-123', 1, 1));
    }

    public function test_getting_single_security_rule()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/security-rules/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "Block Bad Bots", "path": "/admin"}}')
        );

        $rule = $forge->securityRule('org-123', 1, 1, 1);
        $this->assertSame(1, $rule->id);
    }

    public function test_creating_security_rule()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/sites/1/security-rules', [
            'json' => ['name' => 'Rate Limit', 'path' => '/api', 'rule' => 'limit_req'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 2, "name": "Rate Limit", "path": "/api", "rule": "limit_req"}}')
        );

        $rule = $forge->createSecurityRule('org-123', 1, 1, ['name' => 'Rate Limit', 'path' => '/api', 'rule' => 'limit_req']);
        $this->assertSame(2, $rule->id);
    }

    public function test_updating_security_rule()
    {
        $this->expectNotToPerformAssertions();

        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/1/sites/1/security-rules/1', [
            'json' => ['path' => '/admin/*'],
        ])->andReturn(
            new Response(202)
        );

        $forge->updateSecurityRule('org-123', 1, 1, 1, ['path' => '/admin/*']);
    }

    public function test_deleting_security_rule()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/sites/1/security-rules/1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteSecurityRule('org-123', 1, 1, 1);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    // Redirect Rules (4 tests)

    public function test_getting_redirect_rules()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/redirect-rules', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "from": "/old-path", "to": "/new-path", "type": "permanent"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $this->assertCount(1, $forge->redirectRules('org-123', 1, 1));
    }

    public function test_getting_single_redirect_rule()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/redirect-rules/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "from": "/old-path", "to": "/new-path", "type": "permanent"}}')
        );

        $rule = $forge->redirectRule('org-123', 1, 1, 1);
        $this->assertSame(1, $rule->id);
    }

    public function test_creating_redirect_rule()
    {
        $this->expectNotToPerformAssertions();

        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/sites/1/redirect-rules', [
            'json' => ['from' => '/blog', 'to' => '/articles', 'type' => 'redirect'],
        ])->andReturn(
            new Response(202)
        );

        $forge->createRedirectRule('org-123', 1, 1, ['from' => '/blog', 'to' => '/articles', 'type' => 'redirect']);
    }

    public function test_deleting_redirect_rule()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/sites/1/redirect-rules/1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteRedirectRule('org-123', 1, 1, 1);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    // Commands (5 tests)

    public function test_getting_commands()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/commands', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "command": "php artisan migrate", "status": "finished"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $this->assertCount(1, $forge->commands('org-123', 1, 1));
    }

    public function test_getting_single_command()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/commands/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "command": "php artisan migrate", "status": "finished"}}')
        );

        $command = $forge->command('org-123', 1, 1, 1);
        $this->assertSame(1, $command->id);
    }

    public function test_creating_command()
    {
        $this->expectNotToPerformAssertions();

        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/sites/1/commands', [
            'json' => ['command' => 'php artisan cache:clear'],
        ])->andReturn(
            new Response(202)
        );

        $forge->createCommand('org-123', 1, 1, ['command' => 'php artisan cache:clear']);
    }

    public function test_deleting_command()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/sites/1/commands/1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteCommand('org-123', 1, 1, 1);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_command_output()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/commands/1/output', [])->andReturn(
            new Response(200, [], '{"data": {"type": "command-outputs", "id": "1", "attributes": {"output": "Running migrations...\nMigration table created successfully.\nMigrating: 2024_01_01_000000_create_users_table\nMigrated: 2024_01_01_000000_create_users_table (45.67ms)"}}}')
        );

        $output = $forge->commandOutput('org-123', 1, 1, 1);
        $this->assertStringContainsString('Running migrations', $output);
    }

    // Site Configuration (6 tests)

    public function test_getting_site_environment()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/environment', [])->andReturn(
            new Response(200, [], '{"data": {"type": "environments", "id": "1", "attributes": {"content": "APP_NAME=Laravel\nAPP_ENV=production\nAPP_KEY=base64:randomkey123\nAPP_DEBUG=false\nAPP_URL=https://example.com\n\nDB_CONNECTION=mysql\nDB_HOST=127.0.0.1\nDB_PORT=3306\nDB_DATABASE=laravel\nDB_USERNAME=forge\nDB_PASSWORD=secret"}}}')
        );

        $content = $forge->siteEnvironment('org-123', 1, 1);
        $this->assertStringContainsString('APP_NAME=Laravel', $content);
        $this->assertStringContainsString('DB_CONNECTION=mysql', $content);
    }

    public function test_updating_site_environment()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $content = "APP_NAME=MyApp\nAPP_ENV=production\nAPP_KEY=base64:newkey456";

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/1/sites/1/environment', [
            'json' => ['environment' => $content],
        ])->andReturn(
            new Response(200)
        );

        $forge->updateSiteEnvironment('org-123', 1, 1, $content);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_site_nginx()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/nginx', [])->andReturn(
            new Response(200, [], '{"data": {"type": "nginx-configs", "id": "1", "attributes": {"content": "server {\\n    listen 80;\\n    server_name example.com;\\n    root /home/forge/example.com;\\n\\n    location / {\\n        try_files $uri $uri/ /index.php?$query_string;\\n    }\\n}"}}}')
        );

        $content = $forge->siteNginx('org-123', 1, 1);
        $this->assertStringContainsString('server_name example.com', $content);
        $this->assertStringContainsString('location / {', $content);
    }

    public function test_updating_site_nginx()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $content = "server {\n    listen 80;\n    server_name example.com;\n}";

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/1/sites/1/nginx', [
            'json' => ['config' => $content],
        ])->andReturn(
            new Response(200)
        );

        $forge->updateSiteNginx('org-123', 1, 1, $content);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    // Server Logs (2 tests)

    public function test_getting_server_log()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/logs/nginx-error', [])->andReturn(
            new Response(200, [], '{"data": {"type": "server-logs", "id": "1", "attributes": {"content": "2025/11/18 10:00:00 [error] 1234#1234: *1 connect() failed (111: Connection refused)\n2025/11/18 10:01:00 [warn] 1234#1234: *2 upstream server temporarily disabled\n2025/11/18 10:02:00 [error] 1234#1234: *3 open() \\"/var/www/html/favicon.ico\\" failed (2: No such file or directory)"}}}')
        );

        $log = $forge->serverLog('org-123', 1, 'nginx-error');
        $this->assertStringContainsString('Connection refused', $log);
    }

    public function test_deleting_server_log()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/logs/nginx-error', [])->andReturn(
            new Response(204)
        );

        $forge->deleteServerLog('org-123', 1, 'nginx-error');
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    // Site Logs (6 tests)

    public function test_getting_site_nginx_access_log()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/logs/nginx-access', [])->andReturn(
            new Response(200, [], '{"data": {"type": "nginx-access-logs", "id": "1", "attributes": {"content": "192.168.1.1 - - [18/Nov/2025:10:00:00 +0000] \\"GET /api/users HTTP/1.1\\" 200 1234 \\"-\\" \\"Mozilla/5.0\\"\n192.168.1.2 - - [18/Nov/2025:10:01:00 +0000] \\"POST /api/login HTTP/1.1\\" 201 567 \\"-\\" \\"axios/1.6.0\\"\n192.168.1.3 - - [18/Nov/2025:10:02:00 +0000] \\"GET /health HTTP/1.1\\" 200 89 \\"-\\" \\"HealthCheck/1.0\\""}}}')
        );

        $log = $forge->siteNginxAccessLog('org-123', 1, 1);
        $this->assertStringContainsString('GET /api/users', $log);
    }

    public function test_deleting_site_nginx_access_log()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/sites/1/logs/nginx-access', [])->andReturn(
            new Response(204)
        );

        $forge->deleteSiteNginxAccessLog('org-123', 1, 1);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_site_nginx_error_log()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/logs/nginx-error', [])->andReturn(
            new Response(200, [], '{"data": {"type": "nginx-error-logs", "id": "1", "attributes": {"content": "2025/11/18 10:00:00 [error] 5678#5678: *10 FastCGI sent in stderr: \\"PHP message: PHP Fatal error: Uncaught Exception\\"\n2025/11/18 10:01:00 [error] 5678#5678: *11 connect() to unix:/var/run/php/php8.3-fpm.sock failed (2: No such file or directory)\n2025/11/18 10:02:00 [warn] 5678#5678: *12 an upstream response is buffered to a temporary file"}}}')
        );

        $log = $forge->siteNginxErrorLog('org-123', 1, 1);
        $this->assertStringContainsString('FastCGI sent in stderr', $log);
    }

    public function test_deleting_site_nginx_error_log()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/sites/1/logs/nginx-error', [])->andReturn(
            new Response(204)
        );

        $forge->deleteSiteNginxErrorLog('org-123', 1, 1);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    public function test_getting_site_application_log()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/logs/application', [])->andReturn(
            new Response(200, [], '{"data": {"type": "application-logs", "id": "1", "attributes": {"content": "[2025-11-18 10:00:00] production.ERROR: SQLSTATE[HY000] [1045] Access denied for user\n[2025-11-18 10:01:00] production.INFO: User login successful {\\"user_id\\": 123}\n[2025-11-18 10:02:00] production.WARNING: Cache store redis is not available"}}}')
        );

        $log = $forge->siteApplicationLog('org-123', 1, 1);
        $this->assertStringContainsString('User login successful', $log);
    }

    public function test_deleting_site_application_log()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/sites/1/logs/application', [])->andReturn(
            new Response(204)
        );

        $forge->deleteSiteApplicationLog('org-123', 1, 1);
        $this->assertTrue(true); // Assertion to avoid risky test warning
    }

    // Server Actions (8 tests)

    public function test_creating_server_action()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/actions', [
            'json' => ['action' => 'reboot'],
        ])->andReturn(
            new Response(202, [], '{"data": {"id": 1, "status": "pending"}}')
        );

        $action = $forge->createServerAction('org-123', 1, ['action' => 'reboot']);
        $this->assertSame(1, $action['data']['id']);
        $this->assertSame('pending', $action['data']['status']);
    }

    public function test_performing_background_process_action()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/background-processes/1/actions', [
            'json' => ['action' => 'restart'],
        ])->andReturn(
            new Response(202, [], '{"data": {"id": 2, "status": "pending"}}')
        );

        $action = $forge->performBackgroundProcessAction('org-123', 1, 1, ['action' => 'restart']);
        $this->assertSame(2, $action['data']['id']);
        $this->assertSame('pending', $action['data']['status']);
    }

    public function test_performing_nginx_action()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/services/nginx/actions', [
            'json' => ['action' => 'restart'],
        ])->andReturn(
            new Response(202, [], '{"data": {"id": 3, "status": "pending"}}')
        );

        $action = $forge->performNginxAction('org-123', 1, ['action' => 'restart']);
        $this->assertSame(3, $action['data']['id']);
        $this->assertSame('pending', $action['data']['status']);
    }

    public function test_performing_postgres_action()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/services/postgres/actions', [
            'json' => ['action' => 'restart'],
        ])->andReturn(
            new Response(202, [], '{"data": {"id": 4, "status": "pending"}}')
        );

        $action = $forge->performPostgresAction('org-123', 1, ['action' => 'restart']);
        $this->assertSame(4, $action['data']['id']);
        $this->assertSame('pending', $action['data']['status']);
    }

    public function test_performing_redis_action()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/services/redis/actions', [
            'json' => ['action' => 'restart'],
        ])->andReturn(
            new Response(202, [], '{"data": {"id": 5, "status": "pending"}}')
        );

        $action = $forge->performRedisAction('org-123', 1, ['action' => 'restart']);
        $this->assertSame(5, $action['data']['id']);
        $this->assertSame('pending', $action['data']['status']);
    }

    public function test_performing_mysql_action()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/services/mysql/actions', [
            'json' => ['action' => 'restart'],
        ])->andReturn(
            new Response(202, [], '{"data": {"id": 6, "status": "pending"}}')
        );

        $action = $forge->performMySQLAction('org-123', 1, ['action' => 'restart']);
        $this->assertSame(6, $action['data']['id']);
        $this->assertSame('pending', $action['data']['status']);
    }

    public function test_performing_php_action()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/services/php/actions', [
            'json' => ['action' => 'restart'],
        ])->andReturn(
            new Response(202, [], '{"data": {"id": 7, "status": "pending"}}')
        );

        $action = $forge->performPHPAction('org-123', 1, ['action' => 'restart']);
        $this->assertSame(7, $action['data']['id']);
        $this->assertSame('pending', $action['data']['status']);
    }

    public function test_performing_supervisor_action()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/services/supervisor/actions', [
            'json' => ['action' => 'restart'],
        ])->andReturn(
            new Response(202, [], '{"data": {"id": 8, "status": "pending"}}')
        );

        $action = $forge->performSupervisorAction('org-123', 1, ['action' => 'restart']);
        $this->assertSame(8, $action['data']['id']);
        $this->assertSame('pending', $action['data']['status']);
    }

    public function test_action_endpoints_return_empty_array_when_response_has_no_body()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/actions', [
            'json' => ['action' => 'reboot'],
        ])->andReturn(
            new Response(202)
        );

        $this->assertSame([], $forge->createServerAction('org-123', 1, ['action' => 'reboot']));
    }

    public function test_action_endpoints_return_empty_array_when_response_is_not_json()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/actions', [
            'json' => ['action' => 'reboot'],
        ])->andReturn(
            new Response(202, [], 'Accepted')
        );

        $this->assertSame([], $forge->createServerAction('org-123', 1, ['action' => 'reboot']));
    }

    // Additional tests for complete coverage

    public function test_getting_authenticated_user()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'user', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "John Doe", "email": "john@example.com"}}')
        );

        $user = $forge->user();
        $this->assertSame(1, $user->id);
        $this->assertSame('John Doe', $user->name);
    }

    public function test_getting_me_user()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'me', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "John Doe"}}')
        );

        $user = $forge->me();
        $this->assertSame(1, $user->id);
    }

    public function test_getting_single_background_process()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/background-processes/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "command": "php artisan queue:work"}}')
        );

        $process = $forge->backgroundProcess('org-123', 1, 1);
        $this->assertSame(1, $process->id);
    }

    public function test_getting_background_process_log()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/background-processes/1/log', [])->andReturn(
            new Response(200, [], '{"data": {"type": "background-process-logs", "id": "1", "attributes": {"content": "Process log content"}}}')
        );

        $log = $forge->backgroundProcessLog('org-123', 1, 1);
        $this->assertSame('Process log content', $log);
    }

    public function test_getting_single_database()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/database/schemas/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "my_database"}}')
        );

        $database = $forge->database('org-123', 1, 1);
        $this->assertSame(1, $database->id);
    }

    public function test_syncing_databases()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/database/schemas/synchronizations', [])->andReturn(
            new Response(202, [], '{"data": {"status": "syncing"}}')
        );

        $forge->syncDatabases('org-123', 1);
        $this->assertTrue(true);
    }

    public function test_getting_single_database_user()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/database/users/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "forge_user"}}')
        );

        $user = $forge->databaseUser('org-123', 1, 1);
        $this->assertSame(1, $user->id);
    }

    public function test_deleting_database_user()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/database/users/1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteDatabaseUser('org-123', 1, 1);
        $this->assertTrue(true);
    }

    public function test_updating_database_password()
    {
        $this->expectNotToPerformAssertions();

        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/1/database/password', [
            'json' => ['password' => 'newpassword'],
        ])->andReturn(
            new Response(204)
        );

        $forge->updateDatabasePassword('org-123', 1, ['password' => 'newpassword']);
    }

    public function test_getting_octane_integration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/integrations/octane', [])->andReturn(
            new Response(200, [], '{"data": {"enabled": true, "port": 8000}}')
        );

        $octane = $forge->getOctane('org-123', 1, 1);
        $this->assertTrue($octane->enabled);
        $this->assertSame(8000, $octane->port);
    }

    public function test_deleting_octane_integration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/sites/1/integrations/octane', [])->andReturn(
            new Response(204)
        );

        $forge->deleteOctane('org-123', 1, 1);
        $this->assertTrue(true);
    }

    public function test_getting_provider_size()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'providers/1/sizes/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "2GB"}}')
        );

        $size = $forge->providerSize(1, 1);
        $this->assertSame(1, $size->id);
    }

    public function test_getting_provider_region()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'providers/1/regions/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "US East"}}')
        );

        $region = $forge->providerRegion(1, 1);
        $this->assertSame(1, $region->id);
    }

    public function test_getting_provider_region_sizes()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'providers/1/regions/1/sizes', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "name": "2GB"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $sizes = $forge->providerRegionSizes(1, 1);
        $this->assertCount(1, $sizes);
    }

    public function test_getting_provider_region_size()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'providers/1/regions/1/sizes/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "2GB"}}')
        );

        $size = $forge->providerRegionSize(1, 1, 1);
        $this->assertSame(1, $size->id);
    }

    public function test_getting_single_recipe()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/recipes/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "My Recipe"}}')
        );

        $recipe = $forge->recipe('org-123', 1);
        $this->assertSame(1, $recipe->id);
    }

    public function test_getting_team_recipes()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/teams/1/recipes', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "name": "Team Recipe"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $recipes = $forge->teamRecipes('org-123', 1);
        $this->assertCount(1, $recipes);
    }

    public function test_sharing_recipe_with_team()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/teams/1/recipes', [
            'json' => ['recipe_id' => 1],
        ])->andReturn(
            new Response(201, [], '{"data": {"id": 1}}')
        );

        $recipe = $forge->createTeamRecipesShare('org-123', 1, ['recipe_id' => 1]);
        $this->assertSame(1, $recipe->id);
        $this->assertSame('org-123', $recipe->organizationSlug);
        $this->assertSame(1, $recipe->teamId);
    }

    public function test_deleting_recipe_share()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/teams/1/recipes/1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteTeamRecipesShare('org-123', 1, 1);
        $this->assertTrue(true);
    }

    public function test_creating_forge_recipe_run()
    {
        $this->expectNotToPerformAssertions();

        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'forge-recipes/1/runs', [
            'json' => ['server_id' => 1],
        ])->andReturn(
            new Response(202)
        );

        $forge->createForgeRecipeRun(1, ['server_id' => 1]);
    }

    public function test_getting_predefined_role()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'predefined-roles/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "Admin"}}')
        );

        $role = $forge->predefinedRole(1);
        $this->assertSame(1, $role->id);
    }

    public function test_getting_permission()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'permissions/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "server:view"}}')
        );

        $permission = $forge->permission(1);
        $this->assertSame(1, $permission->id);
    }

    public function test_getting_role_permissions()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/roles/1/permissions', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "name": "server:view"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $permissions = $forge->rolePermissions('org-123', 1);
        $this->assertCount(1, $permissions);
    }

    public function test_deleting_server_credential_share()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/teams/1/server-credentials/1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteTeamServerCredentialsShare('org-123', 1, 1);
        $this->assertTrue(true);
    }

    public function test_getting_server_event_output()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/events/1/output', [])->andReturn(
            new Response(200, [], '{"data": {"content": "Event output"}}')
        );

        $output = $forge->serverEventOutput('org-123', 1, 1);
        $this->assertIsArray($output);
    }

    public function test_getting_single_php_version()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/php/versions/83', [])->andReturn(
            new Response(200, [], '{"data": {"id": 83, "version": "8.3"}}')
        );

        $version = $forge->phpVersion('org-123', 1, 83);
        $this->assertSame(83, $version->id);
    }

    public function test_getting_single_domain()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/domains/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "example.com"}}')
        );

        $domain = $forge->domain('org-123', 1, 1, 1);
        $this->assertSame(1, $domain->id);
    }

    public function test_getting_domain_certificates()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/domains/1/certificates', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "status": "active", "active": true}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $certs = $forge->domainCertificates('org-123', 1, 1, 1);
        $this->assertInstanceOf(CursorPaginator::class, $certs);
        $this->assertCount(1, $certs);
    }

    public function test_creating_certificate()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/sites/1/domains/1/certificates', [
            'json' => ['type' => 'letsencrypt'],
        ])->andReturn(
            new Response(202, [], '{"data": {"id": 2, "type": "letsencrypt"}}')
        );

        $cert = $forge->createCertificate('org-123', 1, 1, 1, ['type' => 'letsencrypt']);
        $this->assertSame(2, $cert->id);
    }

    public function test_getting_active_domain_certificate()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/domains/1/certificates/active', [])->andReturn(
            new Response(200, [], '{"data": {"id": 3, "active": true}}')
        );

        $cert = $forge->activeDomainCertificate('org-123', 1, 1, 1);
        $this->assertSame(3, $cert->id);
        $this->assertTrue($cert->active);
    }

    public function test_getting_certificate()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/domains/1/certificates/5', [])->andReturn(
            new Response(200, [], '{"data": {"id": 5, "status": "active"}}')
        );

        $cert = $forge->certificate('org-123', 1, 1, 1, 5);
        $this->assertSame(5, $cert->id);
    }

    public function test_deleting_certificate()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/sites/1/domains/1/certificates/5', [])->andReturn(
            new Response(202)
        );

        $forge->deleteCertificate('org-123', 1, 1, 1, 5);
        $this->expectNotToPerformAssertions();
    }

    public function test_creating_certificate_action()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/sites/1/domains/1/certificates/5/actions', [
            'json' => ['action' => 'renew'],
        ])->andReturn(
            new Response(202)
        );

        $forge->createCertificateAction('org-123', 1, 1, 1, 5, ['action' => 'renew']);
        $this->expectNotToPerformAssertions();
    }

    public function test_getting_team_member()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/teams/1/members/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "John Doe"}}')
        );

        $member = $forge->teamMember('org-123', 1, 1);
        $this->assertSame(1, $member->id);
        $this->assertSame('John Doe', $member->name);
    }

    public function test_updating_team_member()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/teams/1/members/1', [
            'json' => ['role_id' => 7],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "Alice", "email": "alice@example.com"}}')
        );

        $member = $forge->updateTeamMember('org-123', 1, 1, ['role_id' => 7]);
        $this->assertSame(1, $member->id);
        $this->assertSame('Alice', $member->name);
        $this->assertSame('alice@example.com', $member->email);
    }

    public function test_deleting_team_member()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/teams/1/members/1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteTeamMember('org-123', 1, 1);
        $this->assertTrue(true);
    }

    // Deployment methods
    public function test_disabling_quick_deploy()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/sites/1/deployments/status', [])->andReturn(
            new Response(204)
        );

        $forge->disableQuickDeploy('org-123', 1, 1);
        $this->assertTrue(true);
    }

    public function test_enabling_push_to_deploy()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/sites/1/deployments/push-to-deploy', [
            'json' => ['provider' => 'github'],
        ])->andReturn(
            new Response(200, [], '{}')
        );

        $forge->enablePushToDeploy('org-123', 1, 1, ['provider' => 'github']);
        $this->assertTrue(true);
    }

    public function test_disabling_push_to_deploy()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/sites/1/deployments/push-to-deploy', [])->andReturn(
            new Response(204)
        );

        $forge->disablePushToDeploy('org-123', 1, 1);
        $this->assertTrue(true);
    }

    // Recipe team sharing methods
    public function test_creating_team_recipes_share()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/teams/1/recipes', [
            'json' => ['recipe_id' => 1],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "My Recipe"}}')
        );

        $recipe = $forge->createTeamRecipesShare('org-123', 1, ['recipe_id' => 1]);
        $this->assertSame(1, $recipe->id);
    }

    public function test_deleting_team_recipes_share()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/teams/1/recipes/1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteTeamRecipesShare('org-123', 1, 1);
        $this->assertTrue(true);
    }

    // SSH Key methods
    public function test_creating_ssh_key_alias()
    {
        $this->expectNotToPerformAssertions();

        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/ssh-keys', [
            'json' => ['name' => 'Key 1', 'key' => 'ssh-rsa...'],
        ])->andReturn(
            new Response(202)
        );

        $forge->createSshKey('org-123', 1, ['name' => 'Key 1', 'key' => 'ssh-rsa...']);
    }

    public function test_deleting_ssh_key_alias()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/ssh-keys/1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteSshKey('org-123', 1, 1);
        $this->assertTrue(true);
    }

    public function test_getting_server_key()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/key', [])->andReturn(
            new Response(200, [], '{"data": {"type": "server-keys", "id": "1", "attributes": {"public_key": "ssh-rsa..."}}}')
        );

        $key = $forge->serverKey('org-123', 1);
        $this->assertSame('ssh-rsa...', $key);
    }

    public function test_updating_server_key()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/1/key', [
            'json' => ['key' => 'ssh-rsa...'],
        ])->andReturn(
            new Response(200, [], '{"data": {"type": "server-keys", "id": "1", "attributes": {"public_key": "ssh-rsa..."}}}')
        );

        $key = $forge->updateServerKey('org-123', 1, ['key' => 'ssh-rsa...']);
        $this->assertSame('ssh-rsa...', $key);
    }

    // Site Scheduled Jobs
    public function test_getting_site_scheduled_jobs()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/scheduled-jobs', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "command": "php artisan schedule:run"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $jobs = $forge->siteScheduledJobs('org-123', 1, 1);
        $this->assertCount(1, $jobs);
    }

    public function test_creating_site_scheduled_job()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/sites/1/scheduled-jobs', [
            'json' => ['command' => 'php artisan inspire'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "command": "php artisan inspire"}}')
        );

        $job = $forge->createSiteScheduledJob('org-123', 1, 1, ['command' => 'php artisan inspire']);
        $this->assertSame(1, $job->id);
    }

    public function test_getting_site_scheduled_job()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/scheduled-jobs/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "command": "php artisan inspire"}}')
        );

        $job = $forge->siteScheduledJob('org-123', 1, 1, 1);
        $this->assertSame(1, $job->id);
    }

    public function test_deleting_site_scheduled_job()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/sites/1/scheduled-jobs/1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteSiteScheduledJob('org-123', 1, 1, 1);
        $this->assertTrue(true);
    }

    public function test_getting_site_scheduled_job_output()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/scheduled-jobs/1/output', [])->andReturn(
            new Response(200, [], '{"data": {"type": "job-outputs", "id": "1", "attributes": {"output": "Job completed successfully"}}}')
        );

        $output = $forge->siteScheduledJobOutput('org-123', 1, 1, 1);
        $this->assertSame('Job completed successfully', $output);
    }

    // Server Credentials team sharing
    public function test_creating_team_server_credentials_share()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/teams/1/server-credentials', [
            'json' => ['credential_id' => 1],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "AWS Credentials"}}')
        );

        $credential = $forge->createTeamServerCredentialsShare('org-123', 1, ['credential_id' => 1]);
        $this->assertSame(1, $credential->id);
    }

    public function test_deleting_team_server_credentials_share()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/teams/1/server-credentials/1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteTeamServerCredentialsShare('org-123', 1, 1);
        $this->assertTrue(true);
    }

    // Server team sharing
    public function test_creating_team_servers_share()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/teams/1/servers', [
            'json' => ['server_id' => 1],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "Production Server"}}')
        );

        $server = $forge->createTeamServersShare('org-123', 1, ['server_id' => 1]);
        $this->assertSame(1, $server->id);
    }

    public function test_deleting_team_servers_share()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/teams/1/servers/1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteTeamServersShare('org-123', 1, 1);
        $this->assertTrue(true);
    }

    // Sites methods
    public function test_getting_sites()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'sites', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "name": "example.com"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $sites = $forge->sites();
        $this->assertInstanceOf(CursorPaginator::class, $sites);
        $this->assertCount(1, $sites);
    }

    public function test_getting_domain_nginx_config()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/domains/1/nginx', [])->andReturn(
            new Response(200, [], '{"data": {"type": "nginx-configs", "id": "1", "attributes": {"content": "server { ... }"}}}')
        );

        $config = $forge->domainNginxConfig('org-123', 1, 1, 1);
        $this->assertSame('server { ... }', $config);
    }

    public function test_updating_domain_nginx_config()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/1/sites/1/domains/1/nginx', [
            'json' => ['config' => 'server { listen 80; }'],
        ])->andReturn(
            new Response(204)
        );

        $forge->updateDomainNginxConfig('org-123', 1, 1, 1, 'server { listen 80; }');
        $this->assertTrue(true);
    }

    public function test_getting_site_healthcheck()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/healthcheck', [])->andReturn(
            new Response(200, [], '{"data": {"url": "/health", "interval": 60}}')
        );

        $healthcheck = $forge->siteHealthcheck('org-123', 1, 1);
        $this->assertIsArray($healthcheck);
    }

    public function test_updating_site_healthcheck()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/1/sites/1/healthcheck', [
            'json' => ['url' => '/health', 'interval' => 120],
        ])->andReturn(
            new Response(204)
        );

        $forge->updateSiteHealthcheck('org-123', 1, 1, ['url' => '/health', 'interval' => 120]);
        $this->assertTrue(true);
    }

    public function test_getting_site_nginx_config()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/nginx', [])->andReturn(
            new Response(200, [], '{"data": {"type": "nginx-configs", "id": "1", "attributes": {"content": "server { ... }"}}}')
        );

        $config = $forge->siteNginx('org-123', 1, 1);
        $this->assertIsString($config);
        $this->assertSame('server { ... }', $config);
    }

    public function test_updating_site_nginx_config()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/1/sites/1/nginx', [
            'json' => ['config' => 'server { listen 443; }'],
        ])->andReturn(
            new Response(204)
        );

        $forge->updateSiteNginx('org-123', 1, 1, 'server { listen 443; }');
        $this->assertTrue(true);
    }

    public function test_getting_composer_credentials()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/composer/credentials', [])->andReturn(
            new Response(200, [], '{"data": [{"repository": "packagist.org"}]}')
        );

        $credentials = $forge->composerCredentials('org-123', 1, 1);
        $this->assertIsArray($credentials);
        $this->assertContainsOnlyInstancesOf(ComposerCredential::class, $credentials);
    }

    public function test_creating_composer_credential()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/sites/1/composer/credentials', [
            'json' => ['repository' => 'packagist.org', 'username' => 'user'],
        ])->andReturn(
            new Response(200, [], '{"data": {"repository": "packagist.org", "username": "user"}}')
        );

        $credential = $forge->createComposerCredential('org-123', 1, 1, ['repository' => 'packagist.org', 'username' => 'user']);
        $this->assertInstanceOf(ComposerCredential::class, $credential);
        $this->assertSame('packagist.org', $credential->repository);
    }

    public function test_getting_composer_credential()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/composer/credentials/packagist.org', [])->andReturn(
            new Response(200, [], '{"data": {"repository": "packagist.org"}}')
        );

        $credential = $forge->composerCredential('org-123', 1, 1, 'packagist.org');
        $this->assertInstanceOf(ComposerCredential::class, $credential);
        $this->assertSame('packagist.org', $credential->repository);
    }

    public function test_updating_composer_credential()
    {
        $this->expectNotToPerformAssertions();

        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/1/sites/1/composer/credentials/packagist.org', [
            'json' => ['username' => 'newuser'],
        ])->andReturn(
            new Response(202)
        );

        $forge->updateComposerCredential('org-123', 1, 1, 'packagist.org', ['username' => 'newuser']);
    }

    public function test_deleting_composer_credential()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/sites/1/composer/credentials/packagist.org', [])->andReturn(
            new Response(204)
        );

        $forge->deleteComposerCredential('org-123', 1, 1, 'packagist.org');
        $this->assertTrue(true);
    }

    public function test_getting_npm_credentials()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/npm/credentials', [])->andReturn(
            new Response(200, [], '{"data": [{"registry": "registry.npmjs.org"}]}')
        );

        $credentials = $forge->npmCredentials('org-123', 1, 1);
        $this->assertIsArray($credentials);
        $this->assertContainsOnlyInstancesOf(NpmCredential::class, $credentials);
    }

    public function test_creating_npm_credential()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/sites/1/npm/credentials', [
            'json' => ['registry' => 'registry.npmjs.org', 'token' => 'npm_abc123'],
        ])->andReturn(
            new Response(200, [], '{"data": {"registry": "registry.npmjs.org", "token": "npm_abc123"}}')
        );

        $credential = $forge->createNpmCredential('org-123', 1, 1, ['registry' => 'registry.npmjs.org', 'token' => 'npm_abc123']);
        $this->assertInstanceOf(NpmCredential::class, $credential);
        $this->assertSame('registry.npmjs.org', $credential->registry);
    }

    public function test_getting_npm_credential()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/npm/credentials/registry.npmjs.org', [])->andReturn(
            new Response(200, [], '{"data": {"registry": "registry.npmjs.org"}}')
        );

        $credential = $forge->npmCredential('org-123', 1, 1, 'registry.npmjs.org');
        $this->assertInstanceOf(NpmCredential::class, $credential);
        $this->assertSame('registry.npmjs.org', $credential->registry);
    }

    public function test_updating_npm_credential()
    {
        $this->expectNotToPerformAssertions();

        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/1/sites/1/npm/credentials/registry.npmjs.org', [
            'json' => ['token' => 'npm_xyz789'],
        ])->andReturn(
            new Response(202)
        );

        $forge->updateNpmCredential('org-123', 1, 1, 'registry.npmjs.org', ['token' => 'npm_xyz789']);
    }

    public function test_deleting_npm_credential()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/sites/1/npm/credentials/registry.npmjs.org', [])->andReturn(
            new Response(204)
        );

        $forge->deleteNpmCredential('org-123', 1, 1, 'registry.npmjs.org');
        $this->assertTrue(true);
    }

    public function test_getting_load_balancing_nodes()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/load-balancing-nodes', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "server_id": 5, "port": 80, "weight": 1, "backup": false, "down": false}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $nodes = $forge->loadBalancingNodes('org-123', 1, 1);
        $this->assertInstanceOf(CursorPaginator::class, $nodes);
        $this->assertCount(1, $nodes);
        $this->assertSame(1, $nodes[0]->id);
        $this->assertSame(5, $nodes[0]->serverId);
    }

    public function test_updating_load_balancing_nodes()
    {
        $this->expectNotToPerformAssertions();

        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/1/sites/1/load-balancing-nodes', [
            'json' => ['nodes' => ['192.168.1.1', '192.168.1.2']],
        ])->andReturn(
            new Response(202)
        );

        $forge->updateLoadBalancingNodes('org-123', 1, 1, ['nodes' => ['192.168.1.1', '192.168.1.2']]);
    }

    public function test_getting_backup_configurations()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/database/backups', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "name": "Daily Backup"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $backupConfigs = $forge->backupConfigurations('org-123', 1);
        $this->assertInstanceOf(CursorPaginator::class, $backupConfigs);
        $this->assertCount(1, $backupConfigs);
    }

    public function test_getting_backup_configuration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/database/backups/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "Daily Backup"}}')
        );

        $config = $forge->backupConfiguration('org-123', 1, 1);
        $this->assertSame(1, $config->id);
    }

    public function test_creating_backup_configuration()
    {
        $this->expectNotToPerformAssertions();

        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/database/backups', [
            'json' => ['name' => 'Daily Backup', 'provider' => 's3'],
        ])->andReturn(
            new Response(202)
        );

        $forge->createBackupConfiguration('org-123', 1, ['name' => 'Daily Backup', 'provider' => 's3']);
    }

    public function test_updating_backup_configuration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/1/database/backups/1', [
            'json' => ['name' => 'Updated Backup'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "Updated Backup"}}')
        );

        $forge->updateBackupConfiguration('org-123', 1, 1, ['name' => 'Updated Backup']);
        $this->assertTrue(true);
    }

    public function test_deleting_backup_configuration()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/database/backups/1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteBackupConfiguration('org-123', 1, 1);
        $this->assertTrue(true);
    }

    public function test_getting_backups()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/database/backups/1/instances', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "status": "completed"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $this->assertCount(1, $forge->backups('org-123', 1, 1));
    }

    public function test_getting_backup()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/database/backups/1/instances/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "status": "completed"}}')
        );

        $backup = $forge->backup('org-123', 1, 1, 1);
        $this->assertSame(1, $backup->id);
    }

    public function test_creating_backup()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/database/backups/1/instances', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "status": "pending"}}')
        );

        $forge->createBackup('org-123', 1, 1);
        $this->assertTrue(true);
    }

    public function test_deleting_backup()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/database/backups/1/instances/1', [])->andReturn(
            new Response(204)
        );

        $forge->deleteBackup('org-123', 1, 1, 1);
        $this->assertTrue(true);
    }

    public function test_restoring_backup()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/database/backups/1/instances/1/restores', [
            'json' => ['database_id' => 123],
        ])->andReturn(
            new Response(200, [], '{"data": {"status": "restoring"}}')
        );

        $forge->restoreBackup('org-123', 1, 1, 1, ['database_id' => 123]);
        $this->assertTrue(true);
    }

    // =========================================================================
    // Resource hydration tests — verify new properties from Forge API v2
    // =========================================================================

    public function test_database_hydrates_updated_at()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/database/schemas/1', [])->andReturn(
            new Response(200, [], json_encode(['data' => [
                'id' => 1,
                'name' => 'forge',
                'status' => 'installed',
                'created_at' => '2026-01-01T00:00:00Z',
                'updated_at' => '2026-02-01T00:00:00Z',
            ]]))
        );

        $database = $forge->database('org-123', 1, 1);
        $this->assertSame(1, $database->id);
        $this->assertSame('forge', $database->name);
        $this->assertSame('2026-01-01T00:00:00Z', $database->createdAt);
        $this->assertSame('2026-02-01T00:00:00Z', $database->updatedAt);
    }

    public function test_database_user_hydrates_updated_at()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/database/users/1', [])->andReturn(
            new Response(200, [], json_encode(['data' => [
                'id' => 1,
                'name' => 'forge',
                'status' => 'installed',
                'created_at' => '2026-01-01T00:00:00Z',
                'updated_at' => '2026-02-01T00:00:00Z',
            ]]))
        );

        $user = $forge->databaseUser('org-123', 1, 1);
        $this->assertSame(1, $user->id);
        $this->assertSame('2026-02-01T00:00:00Z', $user->updatedAt);
    }

    public function test_background_process_hydrates_directory()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/background-processes/1', [])->andReturn(
            new Response(200, [], json_encode(['data' => [
                'id' => 1,
                'command' => 'node server.js',
                'user' => 'forge',
                'directory' => '/home/forge/app',
                'processes' => 1,
                'status' => 'installed',
                'created_at' => '2026-01-01T00:00:00Z',
            ]]))
        );

        $process = $forge->backgroundProcess('org-123', 1, 1);
        $this->assertSame(1, $process->id);
        $this->assertSame('/home/forge/app', $process->directory);
        $this->assertSame('forge', $process->user);
    }

    public function test_predefined_role_hydrates_timestamps()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'predefined-roles/1', [])->andReturn(
            new Response(200, [], json_encode(['data' => [
                'id' => 1,
                'name' => 'Owner',
                'created_at' => '2026-01-01T00:00:00Z',
                'updated_at' => '2026-02-01T00:00:00Z',
            ]]))
        );

        $role = $forge->predefinedRole(1);
        $this->assertSame(1, $role->id);
        $this->assertSame('Owner', $role->name);
        $this->assertSame('2026-01-01T00:00:00Z', $role->createdAt);
        $this->assertSame('2026-02-01T00:00:00Z', $role->updatedAt);
    }

    // =========================================================================
    // Integration normalization tests
    // =========================================================================

    public function test_get_horizon_normalizes_installed_field()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/integrations/horizon', [])->andReturn(
            new Response(200, [], json_encode(['data' => [
                'enabled' => true,
                'horizon_installed' => true,
            ]]))
        );

        $integration = $forge->getHorizon('org-123', 1, 1);
        $this->assertTrue($integration->enabled);
        $this->assertTrue($integration->installed);
        $this->assertSame('horizon', $integration->type);
        $this->assertSame('org-123', $integration->organizationSlug);
        $this->assertSame(1, $integration->serverId);
        $this->assertSame(1, $integration->siteId);
    }

    public function test_create_horizon_normalizes_installed_field()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/sites/1/integrations/horizon', [])->andReturn(
            new Response(200, [], json_encode(['data' => [
                'enabled' => true,
                'horizon_installed' => false,
            ]]))
        );

        $integration = $forge->createHorizon('org-123', 1, 1);
        $this->assertTrue($integration->enabled);
        $this->assertFalse($integration->installed);
        $this->assertSame('horizon', $integration->type);
    }

    public function test_get_octane_normalizes_installed_field()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/integrations/octane', [])->andReturn(
            new Response(200, [], json_encode(['data' => [
                'enabled' => true,
                'octane_installed' => true,
                'port' => 8000,
            ]]))
        );

        $integration = $forge->getOctane('org-123', 1, 1);
        $this->assertTrue($integration->enabled);
        $this->assertTrue($integration->installed);
        $this->assertSame(8000, $integration->port);
        $this->assertSame('octane', $integration->type);
    }

    public function test_get_reverb_hydrates_host_and_connections()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/integrations/reverb', [])->andReturn(
            new Response(200, [], json_encode(['data' => [
                'enabled' => true,
                'reverb_installed' => true,
                'host' => '0.0.0.0',
                'port' => 6001,
                'connections' => 1000,
            ]]))
        );

        $integration = $forge->getReverb('org-123', 1, 1);
        $this->assertTrue($integration->enabled);
        $this->assertTrue($integration->installed);
        $this->assertSame('0.0.0.0', $integration->host);
        $this->assertSame(6001, $integration->port);
        $this->assertSame(1000, $integration->connections);
        $this->assertSame('reverb', $integration->type);
    }

    public function test_get_maintenance_normalizes_laravel_installed()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/integrations/laravel-maintenance', [])->andReturn(
            new Response(200, [], json_encode(['data' => [
                'enabled' => false,
                'status' => 'inactive',
                'laravel_installed' => true,
            ]]))
        );

        $integration = $forge->getMaintenance('org-123', 1, 1);
        $this->assertFalse($integration->enabled);
        $this->assertTrue($integration->installed);
        $this->assertSame('inactive', $integration->status);
        $this->assertSame('laravel-maintenance', $integration->type);
    }

    public function test_get_scheduler_normalizes_laravel_installed()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/integrations/laravel-scheduler', [])->andReturn(
            new Response(200, [], json_encode(['data' => [
                'enabled' => true,
                'laravel_installed' => true,
            ]]))
        );

        $integration = $forge->getScheduler('org-123', 1, 1);
        $this->assertTrue($integration->enabled);
        $this->assertTrue($integration->installed);
        $this->assertSame('laravel-scheduler', $integration->type);
    }

    public function test_get_pulse_normalizes_installed_field()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/integrations/pulse', [])->andReturn(
            new Response(200, [], json_encode(['data' => [
                'enabled' => false,
                'pulse_installed' => true,
            ]]))
        );

        $integration = $forge->getPulse('org-123', 1, 1);
        $this->assertFalse($integration->enabled);
        $this->assertTrue($integration->installed);
        $this->assertSame('pulse', $integration->type);
    }

    public function test_get_inertia_normalizes_installed_field()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/integrations/inertia', [])->andReturn(
            new Response(200, [], json_encode(['data' => [
                'enabled' => true,
                'inertia_installed' => true,
            ]]))
        );

        $integration = $forge->getInertia('org-123', 1, 1);
        $this->assertTrue($integration->enabled);
        $this->assertTrue($integration->installed);
        $this->assertSame('inertia', $integration->type);
    }

    // =========================================================================
    // JSON:API envelope handling tests
    // =========================================================================

    public function test_integration_normalizes_json_api_wrapped_response()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/1/integrations/horizon', [])->andReturn(
            new Response(200, [], json_encode(['data' => [
                'id' => '5',
                'type' => 'horizonIntegrations',
                'attributes' => [
                    'enabled' => true,
                    'horizon_installed' => false,
                ],
                'links' => ['self' => 'https://forge.test/api/...'],
            ]]))
        );

        $integration = $forge->getHorizon('org-123', 1, 1);
        $this->assertTrue($integration->enabled);
        $this->assertFalse($integration->installed);
        $this->assertSame('horizon', $integration->type);
    }

    public function test_server_json_api_response_preserves_type()
    {
        $server = new Server([
            'id' => '10',
            'type' => 'servers',
            'attributes' => [
                'id' => 10,
                'name' => 'dkr-app-01',
                'type' => 'app',
                'ip_address' => '192.168.1.1',
                'is_ready' => true,
                'created_at' => '2026-01-01T00:00:00Z',
                'updated_at' => '2026-02-01T00:00:00Z',
            ],
            'relationships' => [],
            'links' => ['self' => 'https://forge.test/api/...'],
        ]);

        $this->assertSame(10, $server->id);
        $this->assertSame('dkr-app-01', $server->name);
        $this->assertSame('app', $server->type);
        $this->assertSame('192.168.1.1', $server->ipAddress);
        $this->assertTrue($server->isReady);
        $this->assertSame('2026-02-01T00:00:00Z', $server->updatedAt);
        $this->assertArrayNotHasKey('relationships', $server->attributes);
        $this->assertArrayNotHasKey('links', $server->attributes);
    }

    public function test_database_json_api_response_hydration()
    {
        $database = new Database([
            'id' => '1',
            'type' => 'databases',
            'attributes' => [
                'name' => 'forge',
                'status' => 'installed',
                'created_at' => '2026-01-01T00:00:00Z',
                'updated_at' => '2026-02-01T00:00:00Z',
            ],
            'links' => ['self' => 'https://forge.test/api/...'],
        ]);

        $this->assertSame('forge', $database->name);
        $this->assertSame('installed', $database->status);
        $this->assertSame('2026-01-01T00:00:00Z', $database->createdAt);
        $this->assertSame('2026-02-01T00:00:00Z', $database->updatedAt);
    }

    public function test_monitor_json_api_response_preserves_type()
    {
        $monitor = new Monitor([
            'id' => '1',
            'type' => 'monitors',
            'attributes' => [
                'type' => 'disk',
                'operator' => 'gte',
                'threshold' => 80,
                'minutes' => 5,
                'notify' => 'test@example.com',
                'status' => 'installed',
                'state' => 'OK',
                'created_at' => '2026-01-01T00:00:00Z',
                'updated_at' => '2026-02-01T00:00:00Z',
            ],
        ]);

        $this->assertSame('disk', $monitor->type);
        $this->assertSame('gte', $monitor->operator);
        $this->assertSame(80.0, $monitor->threshold);
        $this->assertSame('installed', $monitor->status);
    }

    public function test_firewall_rule_json_api_response_preserves_type()
    {
        $rule = new FirewallRule([
            'id' => '42',
            'type' => 'rules',
            'attributes' => [
                'name' => 'SSH',
                'port' => '22',
                'type' => 'allow',
                'ip_address' => null,
                'status' => 'installed',
                'created_at' => '2026-01-01T00:00:00Z',
                'updated_at' => '2026-01-01T00:00:00Z',
            ],
        ]);

        $this->assertSame('SSH', $rule->name);
        $this->assertSame('allow', $rule->type);
        $this->assertSame('installed', $rule->status);
    }

    public function test_user_json_api_response_strips_envelope_type()
    {
        $user = new User([
            'id' => '3',
            'type' => 'users',
            'attributes' => [
                'name' => 'Bruno',
                'email' => 'bruno@example.com',
                'created_at' => '2026-01-01T00:00:00Z',
                'updated_at' => '2026-02-01T00:00:00Z',
            ],
            'links' => ['self' => 'https://forge.test/api/user'],
        ]);

        $this->assertSame('Bruno', $user->name);
        $this->assertSame('bruno@example.com', $user->email);
        $this->assertSame('2026-01-01T00:00:00Z', $user->createdAt);
        $this->assertSame('2026-02-01T00:00:00Z', $user->updatedAt);
        $this->assertArrayNotHasKey('type', $user->attributes);
        $this->assertArrayNotHasKey('links', $user->attributes);
    }

    public function test_storage_provider_json_api_response_hydration()
    {
        $provider = new StorageProvider([
            'id' => '1',
            'type' => 'storageProviders',
            'attributes' => [
                'name' => 'My S3',
                'provider' => 's3',
                'provider_name' => 'Amazon S3',
                'region' => 'us-east-1',
                'bucket' => 'my-bucket',
                'directory' => null,
                'endpoint' => null,
                'assume_role' => null,
                'in_use' => false,
                'created_at' => '2026-01-01T00:00:00Z',
                'updated_at' => '2026-02-01T00:00:00Z',
            ],
        ]);

        $this->assertSame('My S3', $provider->name);
        $this->assertSame('s3', $provider->provider);
        $this->assertSame('Amazon S3', $provider->providerName);
        $this->assertSame('us-east-1', $provider->region);
        $this->assertSame('my-bucket', $provider->bucket);
        $this->assertFalse($provider->inUse);
    }

    // =====================================================
    // Resource Convenience Method Tests
    // =====================================================

    public function test_server_delete_convenience_method()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1', [])->andReturn(
            new Response(204)
        );

        $server = new Server(['id' => 1, 'organization_slug' => 'org-123'], $forge);
        $server->delete();

        $this->assertTrue(true);
    }

    public function test_server_reboot_convenience_method()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/actions', [
            'json' => ['action' => 'reboot'],
        ])->andReturn(
            new Response(200, [], '{"data": {"action": "reboot"}}')
        );

        $server = new Server(['id' => 1, 'organization_slug' => 'org-123'], $forge);
        $server->reboot();

        $this->assertTrue(true);
    }

    public function test_server_reboot_mysql_convenience_method()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/services/mysql/actions', [
            'json' => ['action' => 'restart'],
        ])->andReturn(
            new Response(200, [], '{"data": {"action": "restart"}}')
        );

        $server = new Server(['id' => 1, 'organization_slug' => 'org-123'], $forge);
        $server->rebootMysql();

        $this->assertTrue(true);
    }

    public function test_server_stop_mysql_convenience_method()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/services/mysql/actions', [
            'json' => ['action' => 'stop'],
        ])->andReturn(
            new Response(200, [], '{"data": {"action": "stop"}}')
        );

        $server = new Server(['id' => 1, 'organization_slug' => 'org-123'], $forge);
        $server->stopMysql();

        $this->assertTrue(true);
    }

    public function test_server_reboot_postgres_convenience_method()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/services/postgres/actions', [
            'json' => ['action' => 'restart'],
        ])->andReturn(
            new Response(200, [], '{"data": {"action": "restart"}}')
        );

        $server = new Server(['id' => 1, 'organization_slug' => 'org-123'], $forge);
        $server->rebootPostgres();

        $this->assertTrue(true);
    }

    public function test_server_reboot_nginx_convenience_method()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/services/nginx/actions', [
            'json' => ['action' => 'restart'],
        ])->andReturn(
            new Response(200, [], '{"data": {"action": "restart"}}')
        );

        $server = new Server(['id' => 1, 'organization_slug' => 'org-123'], $forge);
        $server->rebootNginx();

        $this->assertTrue(true);
    }

    public function test_server_reboot_php_convenience_method()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/services/php/actions', [
            'json' => ['action' => 'restart'],
        ])->andReturn(
            new Response(200, [], '{"data": {"action": "restart"}}')
        );

        $server = new Server(['id' => 1, 'organization_slug' => 'org-123'], $forge);
        $server->rebootPHP();

        $this->assertTrue(true);
    }

    public function test_server_enable_opcache_convenience_method()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/php/opcache', [])->andReturn(
            new Response(200, [], '{"data": {"enabled": true}}')
        );

        $server = new Server(['id' => 1, 'organization_slug' => 'org-123'], $forge);
        $server->enableOPCache();

        $this->assertTrue(true);
    }

    public function test_server_disable_opcache_convenience_method()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/php/opcache', [])->andReturn(
            new Response(204)
        );

        $server = new Server(['id' => 1, 'organization_slug' => 'org-123'], $forge);
        $server->disableOPCache();

        $this->assertTrue(true);
    }

    public function test_server_php_versions_convenience_method()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/php/versions', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "version": "8.3"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $server = new Server(['id' => 1, 'organization_slug' => 'org-123'], $forge);
        $versions = $server->phpVersions();

        $this->assertInstanceOf(CursorPaginator::class, $versions);
        $this->assertCount(1, $versions);
    }

    public function test_server_install_php_convenience_method()
    {
        $this->expectNotToPerformAssertions();

        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/php/versions', [
            'json' => ['version' => '8.3'],
        ])->andReturn(
            new Response(202)
        );

        $server = new Server(['id' => 1, 'organization_slug' => 'org-123'], $forge);
        $server->installPHP('8.3');
    }

    public function test_site_delete_convenience_method()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/sites/2', [])->andReturn(
            new Response(204)
        );

        $site = new Site(['id' => 2, 'server_id' => 1, 'organization_slug' => 'org-123'], $forge);
        $site->delete();

        $this->assertTrue(true);
    }

    public function test_site_get_deployment_script_convenience_method()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/2/deployments/script', [])->andReturn(
            new Response(200, [], '{"data": {"type": "deployment-scripts", "id": "1", "attributes": {"content": "cd /home/forge && git pull"}}}')
        );

        $site = new Site(['id' => 2, 'server_id' => 1, 'organization_slug' => 'org-123'], $forge);
        $script = $site->getDeploymentScript();

        $this->assertSame('cd /home/forge && git pull', $script);
    }

    public function test_site_update_deployment_script_convenience_method()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/1/sites/2/deployments/script', [
            'json' => ['script' => 'cd /home/forge && git pull'],
        ])->andReturn(
            new Response(200, [], '{"data": {"script": "cd /home/forge && git pull"}}')
        );

        $site = new Site(['id' => 2, 'server_id' => 1, 'organization_slug' => 'org-123'], $forge);
        $site->updateDeploymentScript(['script' => 'cd /home/forge && git pull']);

        $this->assertTrue(true);
    }

    public function test_site_deploy_convenience_method()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/servers/1/sites/2/deployments', [])->andReturn(
            new Response(200, [], '{"data": {"id": 10, "status": "deploying"}}')
        );

        $site = new Site(['id' => 2, 'server_id' => 1, 'organization_slug' => 'org-123'], $forge);
        $deployment = $site->deploySite();

        $this->assertInstanceOf(Deployment::class, $deployment);
    }

    public function test_site_disable_quick_deploy_convenience_method()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/sites/2/deployments/status', [])->andReturn(
            new Response(204)
        );

        $site = new Site(['id' => 2, 'server_id' => 1, 'organization_slug' => 'org-123'], $forge);
        $site->disableQuickDeploy();

        $this->assertTrue(true);
    }

    public function test_site_get_deployment_history_convenience_method()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/2/deployments', [])->andReturn(
            new Response(200, [], '{"data": [{"id": 1, "status": "finished"}], "meta": {"next_cursor": null, "per_page": 15}}')
        );

        $site = new Site(['id' => 2, 'server_id' => 1, 'organization_slug' => 'org-123'], $forge);
        $deployments = $site->getDeploymentHistory();

        $this->assertInstanceOf(CursorPaginator::class, $deployments);
        $this->assertCount(1, $deployments);
    }

    public function test_site_get_deployment_history_output_convenience_method()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/2/deployments/10/log', [])->andReturn(
            new Response(200, [], '{"data": {"type": "deployment-outputs", "id": "10", "attributes": {"output": "Deployment output..."}}}')
        );

        $site = new Site(['id' => 2, 'server_id' => 1, 'organization_slug' => 'org-123'], $forge);
        $output = $site->getDeploymentHistoryOutput(10);

        $this->assertSame('Deployment output...', $output);
    }

    public function test_database_delete_convenience_method()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/database/schemas/5', [])->andReturn(
            new Response(204)
        );

        $database = new Database(['id' => 5, 'server_id' => 1, 'organization_slug' => 'org-123'], $forge);
        $database->delete();

        $this->assertTrue(true);
    }

    public function test_database_user_update_convenience_method()
    {
        $this->expectNotToPerformAssertions();

        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/1/database/users/3', [
            'json' => ['databases' => [1, 2]],
        ])->andReturn(
            new Response(202)
        );

        $user = new DatabaseUser(['id' => 3, 'server_id' => 1, 'organization_slug' => 'org-123'], $forge);
        $user->update(['databases' => [1, 2]]);
    }

    public function test_database_user_delete_convenience_method()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/database/users/3', [])->andReturn(
            new Response(204)
        );

        $user = new DatabaseUser(['id' => 3, 'server_id' => 1, 'organization_slug' => 'org-123'], $forge);
        $user->delete();

        $this->assertTrue(true);
    }

    public function test_domain_delete_convenience_method()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/sites/2/domains/3', [])->andReturn(
            new Response(204)
        );

        $domain = new Domain(['id' => 3, 'server_id' => 1, 'site_id' => 2, 'organization_slug' => 'org-123'], $forge);
        $domain->delete();

        $this->assertTrue(true);
    }

    public function test_firewall_rule_delete_convenience_method()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/firewall-rules/5', [])->andReturn(
            new Response(204)
        );

        $rule = new FirewallRule(['id' => 5, 'server_id' => 1, 'organization_slug' => 'org-123'], $forge);
        $rule->delete();

        $this->assertTrue(true);
    }

    public function test_heartbeat_delete_convenience_method()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/sites/2/heartbeats/3', [])->andReturn(
            new Response(204)
        );

        $heartbeat = new Heartbeat(['id' => 3, 'server_id' => 1, 'site_id' => 2, 'organization_slug' => 'org-123'], $forge);
        $heartbeat->delete();

        $this->assertTrue(true);
    }

    public function test_nginx_template_update_convenience_method()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/servers/1/nginx/templates/5', [
            'json' => ['content' => 'server {}'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 5, "name": "custom"}}')
        );

        $template = new NginxTemplate(['id' => 5, 'server_id' => 1, 'organization_slug' => 'org-123'], $forge);
        $updated = $template->update(['content' => 'server {}']);

        $this->assertInstanceOf(NginxTemplate::class, $updated);
    }

    public function test_nginx_template_delete_convenience_method()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/nginx/templates/5', [])->andReturn(
            new Response(204)
        );

        $template = new NginxTemplate(['id' => 5, 'server_id' => 1, 'organization_slug' => 'org-123'], $forge);
        $template->delete();

        $this->assertTrue(true);
    }

    public function test_recipe_update_convenience_method()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/recipes/5', [
            'json' => ['name' => 'Updated Recipe'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 5, "name": "Updated Recipe"}}')
        );

        $recipe = new Recipe(['id' => 5, 'organization_slug' => 'org-123'], $forge);
        $updated = $recipe->update(['name' => 'Updated Recipe']);

        $this->assertInstanceOf(Recipe::class, $updated);
    }

    public function test_recipe_delete_convenience_method()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/recipes/5', [])->andReturn(
            new Response(204)
        );

        $recipe = new Recipe(['id' => 5, 'organization_slug' => 'org-123'], $forge);
        $recipe->delete();

        $this->assertTrue(true);
    }

    public function test_recipe_run_convenience_method()
    {
        $this->expectNotToPerformAssertions();

        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'orgs/org-123/recipes/5/runs', [
            'json' => ['servers' => [1, 2]],
        ])->andReturn(
            new Response(202)
        );

        $recipe = new Recipe(['id' => 5, 'organization_slug' => 'org-123'], $forge);
        $recipe->run(['servers' => [1, 2]]);
    }

    public function test_redirect_rule_delete_convenience_method()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/sites/2/redirect-rules/3', [])->andReturn(
            new Response(204)
        );

        $rule = new RedirectRule(['id' => 3, 'server_id' => 1, 'site_id' => 2, 'organization_slug' => 'org-123'], $forge);
        $rule->delete();

        $this->assertTrue(true);
    }

    public function test_security_rule_delete_convenience_method()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/sites/2/security-rules/3', [])->andReturn(
            new Response(204)
        );

        $rule = new SecurityRule(['id' => 3, 'server_id' => 1, 'site_id' => 2, 'organization_slug' => 'org-123'], $forge);
        $rule->delete();

        $this->assertTrue(true);
    }

    public function test_ssh_key_delete_convenience_method()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/ssh-keys/5', [])->andReturn(
            new Response(204)
        );

        $key = new SSHKey(['id' => 5, 'server_id' => 1, 'organization_slug' => 'org-123'], $forge);
        $key->delete();

        $this->assertTrue(true);
    }

    public function test_webhook_delete_convenience_method()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('DELETE', 'orgs/org-123/servers/1/sites/2/webhooks/3', [])->andReturn(
            new Response(204)
        );

        $webhook = new Webhook(['id' => 3, 'server_id' => 1, 'site_id' => 2, 'organization_slug' => 'org-123'], $forge);
        $webhook->delete();

        $this->assertTrue(true);
    }

    public function test_organization_slug_hydrated_on_server_from_api()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "Production"}}')
        );

        $server = $forge->server('org-123', 1);
        $this->assertSame('org-123', $server->organizationSlug);
    }

    public function test_organization_slug_hydrated_on_site_from_api()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/sites/2', [])->andReturn(
            new Response(200, [], '{"data": {"id": 2, "name": "example.com"}}')
        );

        $site = $forge->organizationSite('org-123', 2);
        $this->assertSame('org-123', $site->organizationSlug);
    }

    public function test_organization_slug_hydrated_on_database_from_api()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/database/schemas/5', [])->andReturn(
            new Response(200, [], '{"data": {"id": 5, "name": "forge"}}')
        );

        $database = $forge->database('org-123', 1, 5);
        $this->assertSame('org-123', $database->organizationSlug);
        $this->assertSame(1, $database->serverId);
    }

    public function test_organization_slug_hydrated_on_recipe_from_api()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/recipes/5', [])->andReturn(
            new Response(200, [], '{"data": {"id": 5, "name": "Deploy"}}')
        );

        $recipe = $forge->recipe('org-123', 5);
        $this->assertSame('org-123', $recipe->organizationSlug);
    }

    public function test_post_returns_null_for_202_empty_body()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('POST', 'test-endpoint', ['json' => ['key' => 'value']])->andReturn(
            new Response(202)
        );

        $result = $forge->post('test-endpoint', ['key' => 'value']);
        $this->assertNull($result);
    }

    // Issue 1: user() and me() should use ManagesUser trait (passes Forge instance)

    public function test_user_method_returns_user_with_forge_instance()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'user', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "John Doe"}}')
        );

        $user = $forge->user();
        $this->assertInstanceOf(User::class, $user);
        $this->assertSame(1, $user->id);

        // Verify the Forge instance was injected (via reflection since $forge is protected)
        $ref = new \ReflectionProperty($user, 'forge');
        $this->assertSame($forge, $ref->getValue($user));
    }

    public function test_me_method_returns_user_with_forge_instance()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'me', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "John Doe"}}')
        );

        $user = $forge->me();
        $this->assertInstanceOf(User::class, $user);

        $ref = new \ReflectionProperty($user, 'forge');
        $this->assertSame($forge, $ref->getValue($user));
    }

    // Issue 2: Context injection via newResource for get/update methods

    public function test_role_injects_organization_context()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/roles/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "Admin"}}')
        );

        $role = $forge->role('org-123', 1);
        $this->assertInstanceOf(Role::class, $role);
        $this->assertSame('org-123', $role->organizationSlug);
    }

    public function test_update_role_injects_organization_context()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/roles/1', [
            'json' => ['name' => 'Super Admin'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "Super Admin"}}')
        );

        $role = $forge->updateRole('org-123', 1, ['name' => 'Super Admin']);
        $this->assertSame('org-123', $role->organizationSlug);
    }

    public function test_team_injects_organization_context()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/teams/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "Dev Team"}}')
        );

        $team = $forge->team('org-123', 1);
        $this->assertInstanceOf(Team::class, $team);
        $this->assertSame('org-123', $team->organizationSlug);
    }

    public function test_update_team_injects_organization_context()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/teams/1', [
            'json' => ['name' => 'Updated'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "Updated"}}')
        );

        $team = $forge->updateTeam('org-123', 1, ['name' => 'Updated']);
        $this->assertSame('org-123', $team->organizationSlug);
    }

    public function test_team_member_injects_team_context()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/teams/5/members/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "John"}}')
        );

        $member = $forge->teamMember('org-123', 5, 1);
        $this->assertInstanceOf(TeamMember::class, $member);
        $this->assertSame(5, $member->teamId);
    }

    public function test_update_team_member_injects_team_context()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/teams/5/members/1', [
            'json' => ['role' => 'admin'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "role": "admin"}}')
        );

        $member = $forge->updateTeamMember('org-123', 5, 1, ['role' => 'admin']);
        $this->assertSame(5, $member->teamId);
    }

    public function test_team_invitation_injects_team_context()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/teams/5/invites/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "email": "user@example.com"}}')
        );

        $invitation = $forge->teamInvitation('org-123', 5, 1);
        $this->assertInstanceOf(TeamInvitation::class, $invitation);
        $this->assertSame(5, $invitation->teamId);
    }

    public function test_server_credential_injects_organization_context()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/server-credentials/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "AWS"}}')
        );

        $credential = $forge->serverCredential('org-123', 1);
        $this->assertInstanceOf(ServerCredential::class, $credential);
        $this->assertSame('org-123', $credential->organizationSlug);
    }

    public function test_storage_provider_injects_organization_context()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/storage-providers/1', [])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "S3 Bucket"}}')
        );

        $provider = $forge->storageProvider('org-123', 1);
        $this->assertInstanceOf(StorageProvider::class, $provider);
        $this->assertSame('org-123', $provider->organizationSlug);
    }

    public function test_update_storage_provider_injects_organization_context()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('PUT', 'orgs/org-123/storage-providers/1', [
            'json' => ['name' => 'Updated S3'],
        ])->andReturn(
            new Response(200, [], '{"data": {"id": 1, "name": "Updated S3"}}')
        );

        $provider = $forge->updateStorageProvider('org-123', 1, ['name' => 'Updated S3']);
        $this->assertSame('org-123', $provider->organizationSlug);
    }

    // Issue 4: Deployment resource should have serverId populated

    public function test_deployment_has_server_id_from_context()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites/2/deployments/3', [])->andReturn(
            new Response(200, [], '{"data": {"id": 3, "status": "finished"}}')
        );

        $deployment = $forge->deployment('org-123', 1, 2, 3);
        $this->assertInstanceOf(Deployment::class, $deployment);
        $this->assertSame('org-123', $deployment->organizationSlug);
        $this->assertSame(1, $deployment->serverId);
        $this->assertSame(2, $deployment->siteId);
    }

    public function test_get_request_passes_query_parameters_to_guzzle()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers', ['query' => ['cursor' => 'abc']])->andReturn(
            new Response(200, [], '{"data": []}')
        );

        $result = $forge->get('orgs/org-123/servers', ['cursor' => 'abc']);
        $this->assertSame(['data' => []], $result);
    }

    public function test_list_method_returns_cursor_paginator_with_pagination_metadata()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers', [])->andReturn(
            new Response(200, [], json_encode([
                'data' => [
                    ['id' => 1, 'name' => 'Server 1'],
                    ['id' => 2, 'name' => 'Server 2'],
                ],
                'meta' => [
                    'next_cursor' => 'eyJpZCI6Mn0',
                    'per_page' => 2,
                ],
            ]))
        );

        $servers = $forge->servers('org-123');

        $this->assertInstanceOf(CursorPaginator::class, $servers);
        $this->assertCount(2, $servers);
        $this->assertInstanceOf(Server::class, $servers[0]);
        $this->assertSame(1, $servers[0]->id);
        $this->assertSame('Server 2', $servers[1]->name);
        $this->assertSame('eyJpZCI6Mn0', $servers->nextCursor());
        $this->assertTrue($servers->hasMorePages());
        $this->assertSame(2, $servers->perPage());
    }

    public function test_list_method_returns_paginator_with_null_cursor_on_last_page()
    {
        $forge = new Forge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites', [])->andReturn(
            new Response(200, [], json_encode([
                'data' => [
                    ['id' => 1, 'name' => 'example.com'],
                ],
                'meta' => [
                    'next_cursor' => null,
                    'per_page' => 15,
                ],
            ]))
        );

        $sites = $forge->serverSites('org-123', 1);

        $this->assertInstanceOf(CursorPaginator::class, $sites);
        $this->assertCount(1, $sites);
        $this->assertNull($sites->nextCursor());
        $this->assertFalse($sites->hasMorePages());
        $this->assertSame(15, $sites->perPage());
    }

    public function test_paginated_collection_returns_cursor_paginator()
    {
        $forge = new TestableForge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers', [])->andReturn(
            new Response(200, [], json_encode([
                'data' => [
                    ['id' => 1, 'name' => 'Server 1'],
                    ['id' => 2, 'name' => 'Server 2'],
                ],
                'meta' => [
                    'next_cursor' => 'cursor-abc',
                    'per_page' => 10,
                ],
            ]))
        );

        $paginator = $forge->paginatedCollection('orgs/org-123/servers', Server::class, 'org-123');

        $this->assertInstanceOf(CursorPaginator::class, $paginator);
        $this->assertCount(2, $paginator);
        $this->assertInstanceOf(Server::class, $paginator[0]);
        $this->assertSame('Server 1', $paginator[0]->name);
        $this->assertSame('cursor-abc', $paginator->nextCursor());
        $this->assertSame(10, $paginator->perPage());
        $this->assertTrue($paginator->hasMorePages());
    }

    public function test_paginated_collection_passes_query_parameters()
    {
        $forge = new TestableForge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers', ['query' => ['per_page' => 5]])->andReturn(
            new Response(200, [], json_encode([
                'data' => [
                    ['id' => 1, 'name' => 'Server 1'],
                ],
                'meta' => [
                    'next_cursor' => null,
                    'per_page' => 5,
                ],
            ]))
        );

        $paginator = $forge->paginatedCollection(
            'orgs/org-123/servers',
            Server::class,
            'org-123',
            query: ['per_page' => 5],
        );

        $this->assertCount(1, $paginator);
        $this->assertFalse($paginator->hasMorePages());
        $this->assertSame(5, $paginator->perPage());
    }

    public function test_paginated_collection_handles_empty_response()
    {
        $forge = new TestableForge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers', [])->andReturn(
            new Response(200, [], json_encode([
                'data' => [],
                'meta' => [
                    'next_cursor' => null,
                    'per_page' => 10,
                ],
            ]))
        );

        $paginator = $forge->paginatedCollection('orgs/org-123/servers', Server::class, 'org-123');

        $this->assertCount(0, $paginator);
        $this->assertFalse($paginator->hasMorePages());
    }

    public function test_paginated_collection_passes_context_to_resources()
    {
        $forge = new TestableForge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers/1/sites', [])->andReturn(
            new Response(200, [], json_encode([
                'data' => [
                    ['id' => 10, 'name' => 'example.com'],
                ],
                'meta' => [
                    'next_cursor' => null,
                    'per_page' => 10,
                ],
            ]))
        );

        $paginator = $forge->paginatedCollection(
            'orgs/org-123/servers/1/sites',
            Site::class,
            'org-123',
            serverId: 1,
        );

        $this->assertCount(1, $paginator);
        $this->assertInstanceOf(Site::class, $paginator[0]);
        $this->assertSame(1, $paginator[0]->serverId);
    }

    public function test_paginated_collection_handles_missing_meta()
    {
        $forge = new TestableForge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers', [])->andReturn(
            new Response(200, [], json_encode([
                'data' => [
                    ['id' => 1, 'name' => 'Server 1'],
                ],
            ]))
        );

        $paginator = $forge->paginatedCollection('orgs/org-123/servers', Server::class, 'org-123');

        $this->assertCount(1, $paginator);
        $this->assertNull($paginator->nextCursor());
        $this->assertNull($paginator->perPage());
        $this->assertFalse($paginator->hasMorePages());
    }

    public function test_paginated_collection_paginator_can_fetch_next_page()
    {
        $forge = new TestableForge('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers', [])->andReturn(
            new Response(200, [], json_encode([
                'data' => [
                    ['id' => 1, 'name' => 'Server 1'],
                ],
                'meta' => [
                    'next_cursor' => 'cursor-page2',
                    'per_page' => 1,
                ],
            ]))
        );

        $http->shouldReceive('request')->once()->with('GET', 'orgs/org-123/servers', ['query' => ['page' => ['cursor' => 'cursor-page2']]])->andReturn(
            new Response(200, [], json_encode([
                'data' => [
                    ['id' => 2, 'name' => 'Server 2'],
                ],
                'meta' => [
                    'next_cursor' => null,
                    'per_page' => 1,
                ],
            ]))
        );

        $page1 = $forge->paginatedCollection('orgs/org-123/servers', Server::class, 'org-123');
        $page2 = $page1->nextPage();

        $this->assertInstanceOf(CursorPaginator::class, $page2);
        $this->assertCount(1, $page2);
        $this->assertSame('Server 2', $page2[0]->name);
        $this->assertFalse($page2->hasMorePages());
    }
}

/**
 * Exposes protected paginatedCollection() for testing.
 */
class TestableForge extends Forge
{
    public function paginatedCollection(
        string $uri,
        string $class,
        ?string $organizationSlug = null,
        ?int $serverId = null,
        ?int $siteId = null,
        array $extra = [],
        array $query = [],
    ): CursorPaginator {
        return parent::paginatedCollection($uri, $class, $organizationSlug, $serverId, $siteId, $extra, $query);
    }
}
