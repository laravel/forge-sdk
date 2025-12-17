<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\SSHKey;

trait ManagesSSHKeys
{
    /**
     * Get the collection of SSH keys.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @return \Laravel\Forge\Resources\SSHKey[]
     */
    public function sshKeys($organizationId, $serverId)
    {
        return $this->transformCollection(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/ssh-keys")['data'] ?? [],
            SSHKey::class,
            ['organization_id' => $organizationId, 'server_id' => $serverId]
        );
    }

    /**
     * Get a SSH key instance.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $keyId
     * @return \Laravel\Forge\Resources\SSHKey
     */
    public function sshKey($organizationId, $serverId, $keyId)
    {
        return new SSHKey(
            $this->get("orgs/{$organizationId}/servers/{$serverId}/ssh-keys/{$keyId}")['data'] ?? [],
            $this
        );
    }

    /**
     * Create a new SSH key.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @return \Laravel\Forge\Resources\SSHKey
     */
    public function createSshKey($organizationId, $serverId, array $data)
    {
        $key = $this->post("orgs/{$organizationId}/servers/{$serverId}/ssh-keys", $data)['data'] ?? [];

        return new SSHKey($key + ['organization_id' => $organizationId, 'server_id' => $serverId], $this);
    }

    /**
     * Delete the given SSH key.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @param  string  $keyId
     * @return void
     */
    public function deleteSshKey($organizationId, $serverId, $keyId)
    {
        $this->delete("orgs/{$organizationId}/servers/{$serverId}/ssh-keys/{$keyId}");
    }

    /**
     * Get the server's public SSH key.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @return string
     */
    public function serverKey($organizationId, $serverId)
    {
        $response = $this->get("orgs/{$organizationId}/servers/{$serverId}/key");

        return $response['data']['public_key'] ?? $response['public_key'] ?? '';
    }

    /**
     * Update the server's public SSH key.
     *
     * @param  string  $organizationId
     * @param  string  $serverId
     * @return string
     */
    public function updateServerKey($organizationId, $serverId, array $data)
    {
        $response = $this->put("orgs/{$organizationId}/servers/{$serverId}/key", $data);

        return $response['data']['public_key'] ?? $response['public_key'] ?? '';
    }
}
