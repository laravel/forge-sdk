<?php

declare(strict_types=1);

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Team;
use Laravel\Forge\Resources\TeamInvitation;
use Laravel\Forge\Resources\TeamMember;

trait ManagesTeams
{
    /**
     * Get the collection of teams for an organization.
     *
     * @return Team[]
     */
    public function teams(string $organizationSlug): array
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/teams")['data'] ?? [],
            Team::class,
            $organizationSlug,
        );
    }

    /**
     * Get a team.
     */
    public function team(string $organizationSlug, int $teamId): Team
    {
        return new Team($this->get("orgs/{$organizationSlug}/teams/{$teamId}")['data'] ?? [], $this);
    }

    /**
     * Create a new team.
     */
    public function createTeam(string $organizationSlug, array $data): Team
    {
        return $this->newResource(
            Team::class,
            $this->post("orgs/{$organizationSlug}/teams", $data)['data'] ?? [],
            $organizationSlug,
        );
    }

    /**
     * Update a team.
     */
    public function updateTeam(string $organizationSlug, int $teamId, array $data): Team
    {
        return new Team(
            $this->put("orgs/{$organizationSlug}/teams/{$teamId}", $data)['data'] ?? [],
            $this
        );
    }

    /**
     * Delete a team.
     */
    public function deleteTeam(string $organizationSlug, int $teamId): void
    {
        $this->delete("orgs/{$organizationSlug}/teams/{$teamId}");
    }

    /**
     * Get the collection of team members.
     *
     * @return TeamMember[]
     */
    public function teamMembers(string $organizationSlug, int $teamId): array
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/teams/{$teamId}/members")['data'] ?? [],
            TeamMember::class,
            $organizationSlug,
            extra: ['team_id' => $teamId],
        );
    }

    /**
     * Get a team member.
     */
    public function teamMember(string $organizationSlug, int $teamId, int $userId): TeamMember
    {
        return new TeamMember(
            $this->get("orgs/{$organizationSlug}/teams/{$teamId}/members/{$userId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Update a team member.
     */
    public function updateTeamMember(string $organizationSlug, int $teamId, int $userId, array $data): TeamMember
    {
        return new TeamMember(
            $this->put("orgs/{$organizationSlug}/teams/{$teamId}/members/{$userId}", $data)['data'] ?? [],
            $this
        );
    }

    /**
     * Delete a team member.
     */
    public function deleteTeamMember(string $organizationSlug, int $teamId, int $userId): void
    {
        $this->delete("orgs/{$organizationSlug}/teams/{$teamId}/members/{$userId}");
    }

    /**
     * Get the collection of team invitations.
     *
     * @return TeamInvitation[]
     */
    public function teamInvitations(string $organizationSlug, int $teamId): array
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/teams/{$teamId}/invites")['data'] ?? [],
            TeamInvitation::class,
            $organizationSlug,
            extra: ['team_id' => $teamId],
        );
    }

    /**
     * Get a team invitation.
     */
    public function teamInvitation(string $organizationSlug, int $teamId, int $invitationId): TeamInvitation
    {
        return new TeamInvitation(
            $this->get("orgs/{$organizationSlug}/teams/{$teamId}/invites/{$invitationId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a team invitation.
     */
    public function createTeamInvitation(string $organizationSlug, int $teamId, array $data): TeamInvitation
    {
        return $this->newResource(
            TeamInvitation::class,
            $this->post("orgs/{$organizationSlug}/teams/{$teamId}/invites", $data)['data'] ?? [],
            $organizationSlug,
            extra: ['team_id' => $teamId],
        );
    }

    /**
     * Delete a team invitation.
     */
    public function deleteTeamInvitation(string $organizationSlug, int $teamId, int $invitationId): void
    {
        $this->delete("orgs/{$organizationSlug}/teams/{$teamId}/invites/{$invitationId}");
    }
}
