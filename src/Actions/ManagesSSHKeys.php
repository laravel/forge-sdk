<?php

declare(strict_types=1);

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\SSHKey;

trait ManagesSSHKeys
{
    /**
     * Get the collection of SSH keys.
     *
     * @return SSHKey[]
     */
    public function sshKeys(string $organizationSlug, int $serverId): array
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/ssh-keys")['data'] ?? [],
            SSHKey::class,
            $organizationSlug,
            $serverId,
        );
    }

    /**
     * Get a SSH key instance.
     */
    public function sshKey(string $organizationSlug, int $serverId, int $keyId): SSHKey
    {
        return $this->newResource(
            SSHKey::class,
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/ssh-keys/{$keyId}")['data'] ?? [],
            $organizationSlug,
            $serverId,
        );
    }

    /**
     * Create a new SSH key.
     */
    public function createSshKey(string $organizationSlug, int $serverId, array $data): SSHKey
    {
        return $this->newResource(
            SSHKey::class,
            $this->post("orgs/{$organizationSlug}/servers/{$serverId}/ssh-keys", $data)['data'] ?? [],
            $organizationSlug,
            $serverId,
        );
    }

    /**
     * Delete the given SSH key.
     */
    public function deleteSshKey(string $organizationSlug, int $serverId, int $keyId): void
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/ssh-keys/{$keyId}");
    }

    /**
     * Get the server's public SSH key.
     */
    public function serverKey(string $organizationSlug, int $serverId): string
    {
        $response = $this->get("orgs/{$organizationSlug}/servers/{$serverId}/key");

        return $response['data']['public_key'] ?? $response['public_key'] ?? '';
    }

    /**
     * Update the server's public SSH key.
     */
    public function updateServerKey(string $organizationSlug, int $serverId, array $data): string
    {
        $response = $this->put("orgs/{$organizationSlug}/servers/{$serverId}/key", $data);

        return $response['data']['public_key'] ?? $response['public_key'] ?? '';
    }
}
