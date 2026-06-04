<?php

declare(strict_types=1);

namespace Laravel\Forge\Actions;

use Laravel\Forge\CursorPaginator;
use Laravel\Forge\Resources\Team;
use Laravel\Forge\Resources\TeamInvitation;
use Laravel\Forge\Resources\TeamMember;

trait ManagesTeams
{
    /**
     * Get the collection of teams for an organization.
     */
    public function teams(string $organizationSlug, array $query = []): CursorPaginator
    {
        return $this->paginatedCollection(
            "orgs/{$organizationSlug}/teams",
            Team::class,
            $organizationSlug,
            query: $query,
        );
    }

    /**
     * Get a team.
     */
    public function team(string $organizationSlug, int $teamId): Team
    {
        return $this->newResource(
            Team::class,
            $this->get("orgs/{$organizationSlug}/teams/{$teamId}")['data'] ?? [],
            $organizationSlug,
        );
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
        return $this->newResource(
            Team::class,
            $this->put("orgs/{$organizationSlug}/teams/{$teamId}", $data)['data'] ?? [],
            $organizationSlug,
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
     */
    public function teamMembers(string $organizationSlug, int $teamId, array $query = []): CursorPaginator
    {
        return $this->paginatedCollection(
            "orgs/{$organizationSlug}/teams/{$teamId}/members",
            TeamMember::class,
            $organizationSlug,
            extra: ['team_id' => $teamId],
            query: $query,
        );
    }

    /**
     * Get a team member.
     */
    public function teamMember(string $organizationSlug, int $teamId, int $userId): TeamMember
    {
        return $this->newResource(
            TeamMember::class,
            $this->get("orgs/{$organizationSlug}/teams/{$teamId}/members/{$userId}")['data'] ?? [],
            $organizationSlug,
            extra: ['team_id' => $teamId],
        );
    }

    /**
     * Update a team member.
     */
    public function updateTeamMember(string $organizationSlug, int $teamId, int $userId, array $data): TeamMember
    {
        return $this->newResource(
            TeamMember::class,
            $this->put("orgs/{$organizationSlug}/teams/{$teamId}/members/{$userId}", $data)['data'] ?? [],
            $organizationSlug,
            extra: ['team_id' => $teamId],
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
     */
    public function teamInvitations(string $organizationSlug, int $teamId, array $query = []): CursorPaginator
    {
        return $this->paginatedCollection(
            "orgs/{$organizationSlug}/teams/{$teamId}/invites",
            TeamInvitation::class,
            $organizationSlug,
            extra: ['team_id' => $teamId],
            query: $query,
        );
    }

    /**
     * Get a team invitation.
     */
    public function teamInvitation(string $organizationSlug, int $teamId, int $invitationId): TeamInvitation
    {
        return $this->newResource(
            TeamInvitation::class,
            $this->get("orgs/{$organizationSlug}/teams/{$teamId}/invites/{$invitationId}")['data'] ?? [],
            $organizationSlug,
            extra: ['team_id' => $teamId],
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
