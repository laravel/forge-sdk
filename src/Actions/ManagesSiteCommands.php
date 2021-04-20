<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\SiteCommand;

trait ManagesSiteCommands
{
    /**
     * Runs a new site command.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @param  array  $data
     * @return \Laravel\Forge\Resources\SiteCommand
     */
    public function executeSiteCommand($serverId, $siteId, array $data)
    {
        $this->post("servers/$serverId/sites/$siteId/commands", $data);
    }

    /**
     * List commands for a site.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @return \Laravel\Forge\Resources\SiteCommand
     */
    public function listCommandHistory($serverId, $siteId)
    {
        return $this->transformCollection(
            $this->get("servers/$serverId/sites/$siteId/commands")['commands'],
            SiteCommand::class
        );
    }

    /**
     * Get the output for a command.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @param  int  $commandId
     * @return \Laravel\Forge\Resources\SiteCommand
     */
    public function getSiteCommand($serverId, $siteId, $commandId)
    {
        $command = $this->get("servers/$serverId/sites/$siteId/commands/$commandId");

        return $this->transformCollection(
            [$command['command']],
            SiteCommand::class,
            ['output' => $command['output']]
        );
    }
}
