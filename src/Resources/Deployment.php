<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class Deployment extends Resource
{
    /**
     * The id of the organization.
     */
    public ?string $organizationId = null;

    /**
     * The id of the deployment.
     */
    public ?int $id = null;

    /**
     * The id of the site.
     */
    public ?int $siteId = null;

    /**
     * The status of the deployment.
     */
    public ?string $status = null;

    /**
     * The date/time the deployment started.
     */
    public ?string $startedAt = null;

    /**
     * The commit information for the deployment.
     */
    public ?array $commit = null;

    /**
     * The type of the deployment.
     */
    public ?string $type = null;

    /**
     * The date/time the deployment ended.
     */
    public ?string $endedAt = null;

    /**
     * The date/time the deployment was created.
     */
    public ?string $createdAt = null;

    /**
     * The date/time the deployment was last updated.
     */
    public ?string $updatedAt = null;
}
