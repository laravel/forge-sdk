<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

class Certificate extends Resource
{
    /**
     * The id of the certificate.
     */
    public ?int $id = null;

    /**
     * The id of the server.
     */
    public ?int $serverId = null;

    /**
     * The id of the site.
     */
    public ?int $siteId = null;

    /**
     * The type of the certificate.
     */
    public ?string $type = null;

    /**
     * The verification method of the certificate.
     */
    public ?string $verificationMethod = null;

    /**
     * The status of the request.
     */
    public ?string $requestStatus = null;

    /**
     * The status of the certificate.
     */
    public ?string $status = null;

    /**
     * The key type of the certificate.
     */
    public ?string $keyType = null;

    /**
     * The preferred chain of the certificate.
     */
    public ?string $preferredChain = null;

    /**
     * The date/time the certificate was created.
     */
    public ?string $createdAt = null;

    /**
     * The date/time the certificate was last updated.
     */
    public ?string $updatedAt = null;
}
