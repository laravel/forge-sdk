<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\SSHKey;

trait ManagesSSHKeys
{
    /**
     * Get the collection of keys.
     *
     * @param  integer $serverId
     * @return SSHKey[]
     */
    public function keys($serverId)
    {
        return $this->transformCollection(
            $this->get("servers/$serverId/keys")['keys'],
            SSHKey::class,
            ['server_id' => $serverId]
        );
    }

    /**
     * Get an SSH key instance.
     *
     * @param  integer $serverId
     * @param  integer $keyId
     * @return SSHKey
     */
    public function SSHKey($serverId, $keyId)
    {
        return new SSHKey(
            $this->get("servers/$serverId/keys/$keyId")['key'] + ['server_id' => $serverId], $this
        );
    }

    /**
     * Create a new SSH key.
     *
     * @param  integer $serverId
     * @param  array $data
     * @param  boolean $wait
     * @return SSHKey
     */
    public function createSSHKey($serverId, array $data, $wait = true)
    {
        $key = $this->post("servers/$serverId/keys", $data)['key'];

        if ($wait) {
            return $this->retry($this->getTimeout(), function () use ($serverId, $key) {
                $key = $this->SSHKey($serverId, $key['id']);

                return $key->status == 'installed' ? $key : null;
            });
        }

        return new SSHKey($key + ['server_id' => $serverId], $this);
    }

    /**
     * Delete the given key.
     *
     * @param  integer $serverId
     * @param  integer $keyId
     * @return void
     */
    public function deleteSSHKey($serverId, $keyId)
    {
        $this->delete("servers/$serverId/keys/$keyId");
    }
}
