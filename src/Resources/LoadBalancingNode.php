<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class LoadBalancingNode extends Resource
{
    /**
     * The id of the load balancing node.
     */
    public ?int $id = null;

    /**
     * The id of the backend server.
     */
    public ?int $serverId = null;

    /**
     * The port the backend listens on.
     */
    public ?int $port = null;

    /**
     * The weight of the backend in the rotation.
     */
    public ?int $weight = null;

    /**
     * Whether the backend is marked as a backup.
     */
    public ?bool $backup = null;

    /**
     * Whether the backend is marked as down.
     */
    public ?bool $down = null;
}
