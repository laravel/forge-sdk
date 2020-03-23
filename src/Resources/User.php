<?php

namespace Themsaid\Forge\Resources;

class User extends Resource
{
    /**
     * The id of the user.
     *
     * @var integer
     */
    public $id;

    /**
     * The name of the user.
     *
     * @var string
     */
    public $name;

    /**
     * The E-Mail of the user.
     *
     * @var string
     */
    public $email;

    /**
     * Last four digits of user's card.
     *
     * @var string
     */
    public $cardLastFour;

    /**
     * Determines if user connected to GitHub.
     *
     * @var bool
     */
    public $connectedToGithub;

    /**
     * Determines if user connected to GitLab.
     *
     * @var bool
     */
    public $connectedToGitlab;

    /**
     * Determines if user connected to Bitbucket.
     *
     * @var bool
     */
    public $connectedToBitbucket;

    /**
     * Determines if user connected to Bitbucket Two.
     *
     * @var bool
     */
    public $connectedToBitbucketTwo;

    /**
     * Determines if user connected to DigitalOcean.
     *
     * @var bool
     */
    public $connectedToDigitalocean;

    /**
     * Determines if user connected to Linode.
     *
     * @var bool
     */
    public $connectedToLinode;

    /**
     * Determines if user connected to Vultr.
     *
     * @var bool
     */
    public $connectedToVultr;

    /**
     * Determines if user connected to AWS.
     *
     * @var bool
     */
    public $connectedToAws;

    /**
     * Determines if user ready for billing.
     *
     * @var bool
     */
    public $readyForBilling;

    /**
     * Determines if stripe is active.
     *
     * @var integer
     */
    public $stripeIsActive;

    /**
     * Name of stripe plan.
     *
     * @var string
     */
    public $stripePlan;

    /**
     * Determines if user is subscribed.
     *
     * @var integer
     */
    public $subscribed;

    /**
     * Determines if user can create servers.
     *
     * @var bool
     */
    public $canCreateServers;
}