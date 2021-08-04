<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Region;

trait ManagesRegions
{
    /**
     * Get the collection of regions.
     *
     * @return \Laravel\Forge\Resources\Region[]
     */
    public function regions()
    {
        return $this->transformCollection(
            $this->get('regions')['regions'], Region::class
        );
    }
}
