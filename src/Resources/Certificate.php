<?php

namespace Laravel\Forge\Resources;

class Certificate extends Resource
{
    /**
     * The id of the certificate.
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
     * The domain name.
     *
     * @var string
     */
    public $domain;

    /**
     * The type of the certificate.
     *
     * @var string
     */
    public $type;

    /**
     * Determine if the certificate is an existing one.
     *
     * @var bool
     */
    public $existing;

    /**
     * The status of the request.
     *
     * @var string
     */
    public $requestStatus;

    /**
     * The status of the certificate.
     *
     * @var string
     */
    public $status;

    /**
     * Determine if the certificate is active.
     *
     * @var bool
     */
    public $active;

    /**
     * Activation status of the certificate.
     *
     * @var string
     */
    public $activationStatus;

    /**
     * The date/time the certificate was created.
     *
     * @var string
     */
    public $createdAt;

    /**
     * Delete the given certificate.
     *
     * @return void
     */
    public function delete()
    {
        $this->forge->deleteCertificate($this->serverId, $this->siteId, $this->id);
    }

    /**
     * Get the SSL certificate signing request for the site.
     *
     * @return string
     */
    public function getSigningRequest()
    {
        return $this->forge->getCertificateSigningRequest($this->serverId, $this->siteId, $this->id);
    }

    /**
     * Install the given certificate for the site.
     *
     * @param  array  $data
     * @param  bool  $wait
     * @return void
     */
    public function install(array $data, $wait = true)
    {
        $this->forge->installCertificate($this->serverId, $this->siteId, $this->id, $data, $wait);
    }

    /**
     * Activate the given certificate for the site.
     *
     * @param  bool  $wait
     * @return void
     */
    public function activate($wait = true)
    {
        $this->forge->activateCertificate($this->serverId, $this->siteId, $this->id, $wait);
    }
}
