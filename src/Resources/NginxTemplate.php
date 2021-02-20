<?php

namespace Laravel\Forge\Resources;

class NginxTemplate extends Resource
{
    /**
     * The id of the nginx template.
     *
     * @var int
     */
    public $id;

    /**
     * The id of the server.
     *
     * @var int
     */
    public $serverId;

    /**
     * The name of the nginx template.
     *
     * @var string
     */
    public $name;

    /**
     * Update the given nginx template.
     *
     * @param  array  $data
     * @return \Laravel\Forge\Resources\NginxTemplate
     */
    public function update(array $data)
    {
        return $this->forge->updateNginxTemplate($this->serverId, $this->id, $data);
    }

    /**
     * Delete the given nginx template.
     *
     * @return void
     */
    public function delete()
    {
        $this->forge->deleteNginxTemplate($this->serverId, $this->id);
    }
}
