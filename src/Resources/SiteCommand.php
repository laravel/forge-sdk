<?php

namespace Laravel\Forge\Resources;

class SiteCommand extends Resource
{
    /**
     * The id of the command.
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
     * The id of the site.
     *
     * @var int
     */
    public $siteId;

    /**
     * The id of the user.
     *
     * @var int
     */
    public $userId;

    /**
     * The id of the event.
     *
     * @var int
     */
    public $eventId;

    /**
     * The command body.
     *
     * @var string
     */
    public $command;

    /**
     * The status of command execution.
     *
     * @var string
     */
    public $status;
    
    /**
     * The created_at time for the command.
     *
     * @var string
     */
    public $createdAt;
    
    /**
     * The The updated_at time for the command.
     *
     * @var string
     */
    public $updatedAt;
    
    /**
     * The URL for the user creating the command.
     *
     * @var string
     */
    public $profilePhotoUrl;
      
    /**
     * The user_name for the user creating the command.
     *
     * @var string
     */
    public $userName;
      
    /**
     * The output from the command
     *
     * @var string
     */
    public $output;

    /**
     * Create a new site command.
     *
     * @param  array  $data
     * @return void
     */
    public function create(array $data)
    {
        $this->forge->createSiteCommand($this->serverId, $this->siteId, $data);
    }

    /**
     * List all commands for a given server.
     *
     * @param  bool  $wait
     * @return void
     */
    public function list($wait = true)
    {
        $this->forge->listCommandHistory($this->serverId, $this->siteId);
    }

    /**
     * Get the output of the specified command.
     *
     * @return string
     */
    public function getCommand()
    {
        return $this->forge->getSiteCommand($this->serverId, $this->siteId, $this->id);
    }
}
