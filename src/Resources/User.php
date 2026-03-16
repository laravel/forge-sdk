<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class User extends Resource
{
    /**
     * The id of the user.
     */
    public ?int $id = null;

    /**
     * The name of the user.
     */
    public ?string $name = null;

    /**
     * The E-Mail of the user.
     */
    public ?string $email = null;

    /**
     * Last four digits of user's card.
     */
    public ?string $cardLastFour = null;

    /**
     * Determines if user connected to GitHub.
     */
    public ?bool $connectedToGithub = null;

    /**
     * Determines if user connected to GitLab.
     */
    public ?bool $connectedToGitlab = null;

    /**
     * Determines if user connected to Bitbucket.
     */
    public ?bool $connectedToBitbucket = null;

    /**
     * Determines if user connected to Bitbucket Two.
     */
    public ?bool $connectedToBitbucketTwo = null;

    /**
     * Determines if user connected to DigitalOcean.
     */
    public ?bool $connectedToDigitalocean = null;

    /**
     * Determines if user connected to Linode.
     */
    public ?bool $connectedToLinode = null;

    /**
     * Determines if user connected to Vultr.
     */
    public ?bool $connectedToVultr = null;

    /**
     * Determines if user connected to AWS.
     */
    public ?bool $connectedToAws = null;

    /**
     * Determines if user ready for billing.
     */
    public ?bool $readyForBilling = null;

    /**
     * Determines if stripe is active.
     */
    public ?int $stripeIsActive = null;

    /**
     * Name of stripe plan.
     */
    public ?string $stripePlan = null;

    /**
     * Determines if user is subscribed.
     */
    public ?int $subscribed = null;

    /**
     * Determines if user can create servers.
     */
    public ?bool $canCreateServers = null;

    /**
     * The date/time the user was created.
     */
    public ?string $createdAt = null;

    /**
     * The date/time the user was last updated.
     */
    public ?string $updatedAt = null;
}
