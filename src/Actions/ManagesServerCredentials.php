<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\ServerCredential;

trait ManagesServerCredentials
{
    /**
     * Get the collection of team server credentials.
     *
     * @param  string  $organizationSlug
     * @param  string  $teamId
     * @return \Laravel\Forge\Resources\ServerCredential[]
     */
    public function teamServerCredentials($organizationSlug, $teamId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/teams/{$teamId}/server-credentials")['data'] ?? [],
            ServerCredential::class,
            ['organization_id' => $organizationSlug, 'team_id' => $teamId]
        );
    }

    /**
     * Share a server credential with a team (create team server credentials share).
     *
     * @param  string  $organizationSlug
     * @param  string  $teamId
     * @return \Laravel\Forge\Resources\ServerCredential
     */
    public function createTeamServerCredentialsShare($organizationSlug, $teamId, array $data)
    {
        $credential = $this->post("orgs/{$organizationSlug}/teams/{$teamId}/server-credentials", $data)['data'] ?? [];

        return new ServerCredential($credential + ['organization_id' => $organizationSlug, 'team_id' => $teamId], $this);
    }

    /**
     * Remove a server credential share from a team (delete team server credentials share).
     *
     * @param  string  $organizationSlug
     * @param  string  $teamId
     * @param  string  $credentialId
     * @return void
     */
    public function deleteTeamServerCredentialsShare($organizationSlug, $teamId, $credentialId)
    {
        $this->delete("orgs/{$organizationSlug}/teams/{$teamId}/server-credentials/{$credentialId}");
    }
}
