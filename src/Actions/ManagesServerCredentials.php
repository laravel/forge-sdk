<?php

declare(strict_types=1);

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\ServerCredential;

trait ManagesServerCredentials
{
    /**
     * Get the collection of team server credentials.
     *
     * @return ServerCredential[]
     */
    public function teamServerCredentials(string $organizationSlug, int $teamId): array
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/teams/{$teamId}/server-credentials")['data'] ?? [],
            ServerCredential::class,
            $organizationSlug,
            extra: ['team_id' => $teamId],
        );
    }

    /**
     * Share a server credential with a team (create team server credentials share).
     */
    public function createTeamServerCredentialsShare(string $organizationSlug, int $teamId, array $data): ServerCredential
    {
        return $this->newResource(
            ServerCredential::class,
            $this->post("orgs/{$organizationSlug}/teams/{$teamId}/server-credentials", $data)['data'] ?? [],
            $organizationSlug,
            extra: ['team_id' => $teamId],
        );
    }

    /**
     * Remove a server credential share from a team (delete team server credentials share).
     */
    public function deleteTeamServerCredentialsShare(string $organizationSlug, int $teamId, int $credentialId): void
    {
        $this->delete("orgs/{$organizationSlug}/teams/{$teamId}/server-credentials/{$credentialId}");
    }
}
