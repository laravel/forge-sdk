<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Team;
use Laravel\Forge\Resources\TeamInvitation;
use Laravel\Forge\Resources\TeamMember;

trait ManagesTeams
{
    /**
     * Get the collection of teams for an organization.
     *
     * @param  string  $organizationSlug
     * @return \Laravel\Forge\Resources\Team[]
     */
    public function teams($organizationSlug)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/teams")['data'] ?? [],
            Team::class,
            ['organization_id' => $organizationSlug]
        );
    }

    /**
     * Get a team.
     *
     * @param  string  $organizationSlug
     * @param  string  $teamId
     * @return \Laravel\Forge\Resources\Team
     */
    public function team($organizationSlug, $teamId)
    {
        return new Team($this->get("orgs/{$organizationSlug}/teams/{$teamId}")['data'] ?? [], $this);
    }

    /**
     * Create a new team.
     *
     * @param  string  $organizationSlug
     * @return \Laravel\Forge\Resources\Team
     */
    public function createTeam($organizationSlug, array $data)
    {
        $team = $this->post("orgs/{$organizationSlug}/teams", $data)['data'] ?? [];

        return new Team($team + ['organization_id' => $organizationSlug], $this);
    }

    /**
     * Update a team.
     *
     * @param  string  $organizationSlug
     * @param  string  $teamId
     * @return \Laravel\Forge\Resources\Team
     */
    public function updateTeam($organizationSlug, $teamId, array $data)
    {
        $team = $this->put("orgs/{$organizationSlug}/teams/{$teamId}", $data)['data'] ?? [];

        return new Team($team, $this);
    }

    /**
     * Delete a team.
     *
     * @param  string  $organizationSlug
     * @param  string  $teamId
     * @return void
     */
    public function deleteTeam($organizationSlug, $teamId)
    {
        $this->delete("orgs/{$organizationSlug}/teams/{$teamId}");
    }

    /**
     * Get the collection of team members.
     *
     * @param  string  $organizationSlug
     * @param  string  $teamId
     * @return \Laravel\Forge\Resources\TeamMember[]
     */
    public function teamMembers($organizationSlug, $teamId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/teams/{$teamId}/members")['data'] ?? [],
            TeamMember::class,
            ['organization_id' => $organizationSlug, 'team_id' => $teamId]
        );
    }

    /**
     * Get a team member.
     *
     * @param  string  $organizationSlug
     * @param  string  $teamId
     * @param  string  $userId
     * @return \Laravel\Forge\Resources\TeamMember
     */
    public function teamMember($organizationSlug, $teamId, $userId)
    {
        return new TeamMember(
            $this->get("orgs/{$organizationSlug}/teams/{$teamId}/members/{$userId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Update a team member.
     *
     * @param  string  $organizationSlug
     * @param  string  $teamId
     * @param  string  $userId
     * @return \Laravel\Forge\Resources\TeamMember
     */
    public function updateTeamMember($organizationSlug, $teamId, $userId, array $data)
    {
        $member = $this->put("orgs/{$organizationSlug}/teams/{$teamId}/members/{$userId}", $data)['data'] ?? [];

        return new TeamMember($member, $this);
    }

    /**
     * Delete a team member.
     *
     * @param  string  $organizationSlug
     * @param  string  $teamId
     * @param  string  $userId
     * @return void
     */
    public function deleteTeamMember($organizationSlug, $teamId, $userId)
    {
        $this->delete("orgs/{$organizationSlug}/teams/{$teamId}/members/{$userId}");
    }

    /**
     * Get the collection of team invitations.
     *
     * @param  string  $organizationSlug
     * @param  string  $teamId
     * @return \Laravel\Forge\Resources\TeamInvitation[]
     */
    public function teamInvitations($organizationSlug, $teamId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/teams/{$teamId}/invites")['data'] ?? [],
            TeamInvitation::class,
            ['organization_id' => $organizationSlug, 'team_id' => $teamId]
        );
    }

    /**
     * Get a team invitation.
     *
     * @param  string  $organizationSlug
     * @param  string  $teamId
     * @param  string  $invitationId
     * @return \Laravel\Forge\Resources\TeamInvitation
     */
    public function teamInvitation($organizationSlug, $teamId, $invitationId)
    {
        return new TeamInvitation(
            $this->get("orgs/{$organizationSlug}/teams/{$teamId}/invites/{$invitationId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a team invitation.
     *
     * @param  string  $organizationSlug
     * @param  string  $teamId
     * @return \Laravel\Forge\Resources\TeamInvitation
     */
    public function createTeamInvitation($organizationSlug, $teamId, array $data)
    {
        $invitation = $this->post("orgs/{$organizationSlug}/teams/{$teamId}/invites", $data)['data'] ?? [];

        return new TeamInvitation($invitation + ['organization_id' => $organizationSlug, 'team_id' => $teamId], $this);
    }

    /**
     * Delete a team invitation.
     *
     * @param  string  $organizationSlug
     * @param  string  $teamId
     * @param  string  $invitationId
     * @return void
     */
    public function deleteTeamInvitation($organizationSlug, $teamId, $invitationId)
    {
        $this->delete("orgs/{$organizationSlug}/teams/{$teamId}/invites/{$invitationId}");
    }
}
