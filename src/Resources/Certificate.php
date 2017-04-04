<?php

namespace Themsaid\Forge\Resources;

class Certificate extends Resource
{
    /**
     * The id of the certificate.
     *
     * @var integer
     */
    public $id;

    /**
     * The id of the server.
     *
     * @var integer
     */
    public $serverId;

    /**
     * The id of the site.
     *
     * @var integer
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
     * @var boolean
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
     * @var boolean
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
        return $this->forge->deleteCertificate($this->serverId, $this->siteId, $this->id);
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
     * @param  boolean $wait
     * @return void
     */
    public function install($wait = true)
    {
        return $this->forge->installCertificate($this->serverId, $this->siteId, $this->id, $wait);
    }

    /**
     * Activate the given certificate for the site.
     *
     * @param  boolean $wait
     * @return void
     */
    public function activate($wait = true)
    {
        return $this->forge->activateCertificate($this->serverId, $this->siteId, $this->id, $wait);
    }
}