<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Team;
use Laravel\Forge\Resources\TeamMember;
use Laravel\Forge\Resources\TeamInvitation;

trait ManagesTeams
{
    /**
     * Get the collection of teams for an organization.
     *
     * @param  string  $organizationId
     * @return \Laravel\Forge\Resources\Team[]
     */
    public function teams($organizationId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationId}/teams")['data'] ?? [],
            Team::class,
            ['organization_id' => $organizationId]
        );
    }

    /**
     * Get a team.
     *
     * @param  string  $organizationId
     * @param  string  $teamId
     * @return \Laravel\Forge\Resources\Team
     */
    public function team($organizationId, $teamId)
    {
        return new Team($this->get("orgs/{$organizationId}/teams/{$teamId}")['data'] ?? [], $this);
    }

    /**
     * Create a new team.
     *
     * @param  string  $organizationId
     * @param  array  $data
     * @return \Laravel\Forge\Resources\Team
     */
    public function createTeam($organizationId, array $data)
    {
        $team = $this->post("orgs/{$organizationId}/teams", $data)['data'] ?? [];

        return new Team($team + ['organization_id' => $organizationId], $this);
    }

    /**
     * Update a team.
     *
     * @param  string  $organizationId
     * @param  string  $teamId
     * @param  array  $data
     * @return \Laravel\Forge\Resources\Team
     */
    public function updateTeam($organizationId, $teamId, array $data)
    {
        $team = $this->put("orgs/{$organizationId}/teams/{$teamId}", $data)['data'] ?? [];

        return new Team($team, $this);
    }

    /**
     * Delete a team.
     *
     * @param  string  $organizationId
     * @param  string  $teamId
     * @return void
     */
    public function deleteTeam($organizationId, $teamId)
    {
        $this->delete("orgs/{$organizationId}/teams/{$teamId}");
    }

    /**
     * Get the collection of team members.
     *
     * @param  string  $organizationId
     * @param  string  $teamId
     * @return \Laravel\Forge\Resources\TeamMember[]
     */
    public function teamMembers($organizationId, $teamId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationId}/teams/{$teamId}/members")['data'] ?? [],
            TeamMember::class,
            ['organization_id' => $organizationId, 'team_id' => $teamId]
        );
    }

    /**
     * Get a team member.
     *
     * @param  string  $organizationId
     * @param  string  $teamId
     * @param  string  $userId
     * @return \Laravel\Forge\Resources\TeamMember
     */
    public function teamMember($organizationId, $teamId, $userId)
    {
        return new TeamMember(
            $this->get("orgs/{$organizationId}/teams/{$teamId}/members/{$userId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Update a team member.
     *
     * @param  string  $organizationId
     * @param  string  $teamId
     * @param  string  $userId
     * @param  array  $data
     * @return \Laravel\Forge\Resources\TeamMember
     */
    public function updateTeamMember($organizationId, $teamId, $userId, array $data)
    {
        $member = $this->put("orgs/{$organizationId}/teams/{$teamId}/members/{$userId}", $data)['data'] ?? [];

        return new TeamMember($member, $this);
    }

    /**
     * Delete a team member.
     *
     * @param  string  $organizationId
     * @param  string  $teamId
     * @param  string  $userId
     * @return void
     */
    public function deleteTeamMember($organizationId, $teamId, $userId)
    {
        $this->delete("orgs/{$organizationId}/teams/{$teamId}/members/{$userId}");
    }

    /**
     * Get the collection of team invitations.
     *
     * @param  string  $organizationId
     * @param  string  $teamId
     * @return \Laravel\Forge\Resources\TeamInvitation[]
     */
    public function teamInvitations($organizationId, $teamId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationId}/teams/{$teamId}/invites")['data'] ?? [],
            TeamInvitation::class,
            ['organization_id' => $organizationId, 'team_id' => $teamId]
        );
    }

    /**
     * Get a team invitation.
     *
     * @param  string  $organizationId
     * @param  string  $teamId
     * @param  string  $invitationId
     * @return \Laravel\Forge\Resources\TeamInvitation
     */
    public function teamInvitation($organizationId, $teamId, $invitationId)
    {
        return new TeamInvitation(
            $this->get("orgs/{$organizationId}/teams/{$teamId}/invites/{$invitationId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a team invitation.
     *
     * @param  string  $organizationId
     * @param  string  $teamId
     * @param  array  $data
     * @return \Laravel\Forge\Resources\TeamInvitation
     */
    public function createTeamInvitation($organizationId, $teamId, array $data)
    {
        $invitation = $this->post("orgs/{$organizationId}/teams/{$teamId}/invites", $data)['data'] ?? [];

        return new TeamInvitation($invitation + ['organization_id' => $organizationId, 'team_id' => $teamId], $this);
    }

    /**
     * Delete a team invitation.
     *
     * @param  string  $organizationId
     * @param  string  $teamId
     * @param  string  $invitationId
     * @return void
     */
    public function deleteTeamInvitation($organizationId, $teamId, $invitationId)
    {
        $this->delete("orgs/{$organizationId}/teams/{$teamId}/invites/{$invitationId}");
    }
}
