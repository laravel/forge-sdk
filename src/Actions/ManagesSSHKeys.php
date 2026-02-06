<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\SSHKey;

trait ManagesSSHKeys
{
    /**
     * Get the collection of SSH keys.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return \Laravel\Forge\Resources\SSHKey[]
     */
    public function sshKeys($organizationSlug, $serverId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/ssh-keys")['data'] ?? [],
            SSHKey::class,
            ['organization_id' => $organizationSlug, 'server_id' => $serverId]
        );
    }

    /**
     * Get a SSH key instance.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $keyId
     * @return \Laravel\Forge\Resources\SSHKey
     */
    public function sshKey($organizationSlug, $serverId, $keyId)
    {
        return new SSHKey(
            $this->get("orgs/{$organizationSlug}/servers/{$serverId}/ssh-keys/{$keyId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a new SSH key.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return \Laravel\Forge\Resources\SSHKey
     */
    public function createSshKey($organizationSlug, $serverId, array $data)
    {
        $key = $this->post("orgs/{$organizationSlug}/servers/{$serverId}/ssh-keys", $data)['data'] ?? [];

        return new SSHKey($key + ['organization_id' => $organizationSlug, 'server_id' => $serverId], $this);
    }

    /**
     * Delete the given SSH key.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @param  string  $keyId
     * @return void
     */
    public function deleteSshKey($organizationSlug, $serverId, $keyId)
    {
        $this->delete("orgs/{$organizationSlug}/servers/{$serverId}/ssh-keys/{$keyId}");
    }

    /**
     * Get the server's public SSH key.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return string
     */
    public function serverKey($organizationSlug, $serverId)
    {
        $response = $this->get("orgs/{$organizationSlug}/servers/{$serverId}/key");

        return $response['data']['public_key'] ?? $response['public_key'] ?? '';
    }

    /**
     * Update the server's public SSH key.
     *
     * @param  string  $organizationSlug
     * @param  string  $serverId
     * @return string
     */
    public function updateServerKey($organizationSlug, $serverId, array $data)
    {
        $response = $this->put("orgs/{$organizationSlug}/servers/{$serverId}/key", $data);

        return $response['data']['public_key'] ?? $response['public_key'] ?? '';
    }
}
