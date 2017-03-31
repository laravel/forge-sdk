<?php

namespace Themsaid\Forge\Actions;

use Themsaid\Forge\Resources\Certificate;

trait ManagesCertificates
{
    /**
     * Get the collection of certificates.
     *
     * @param  integer $serverId
     * @param  integer $siteId
     * @return Certificate[]
     */
    public function certificates($serverId, $siteId)
    {
        return $this->transformCollection(
            $this->get("servers/$serverId/sites/$siteId/certificates")['certificates'],
            Certificate::class,
            ['server_id' => $serverId, 'site_id' => $siteId]
        );
    }

    /**
     * Get a certificate instance.
     *
     * @param  integer $serverId
     * @param  integer $siteId
     * @param  integer $certificateId
     * @return Certificate
     */
    public function certificate($serverId, $siteId, $certificateId)
    {
        return new Certificate(
            $this->get("servers/$serverId/sites/$siteId/certificates/$certificateId")['certificate']
            + ['server_id' => $serverId, 'site_id' => $siteId], $this
        );
    }

    /**
     * Create a new certificate.
     *
     * @param  integer $serverId
     * @param  integer $siteId
     * @param  array $data
     * @param  boolean $wait
     * @return Certificate
     */
    public function createCertificate($serverId, $siteId, array $data, $wait = true)
    {
        $certificate = $this->post("servers/$serverId/sites/$siteId/certificates", $data)['certificate'];

        if ($wait) {
            return $this->retry(30, function () use ($serverId, $siteId, $certificate) {
                $certificate = $this->certificate($serverId, $siteId, $certificate['id']);

                return $certificate->status == 'installed' ? $certificate : null;
            });
        }

        return new Certificate($certificate + ['server_id' => $serverId, 'site_id' => $siteId], $this);
    }

    /**
     * Delete the given certificate.
     *
     * @param  integer $serverId
     * @param  integer $siteId
     * @param  integer $certificateId
     * @return void
     */
    public function deleteCertificate($serverId, $siteId, $certificateId)
    {
        $this->delete("servers/$serverId/sites/$siteId/certificates/$certificateId");
    }

    /**
     * Get the SSL certificate signing request for the site.
     *
     * @param  integer $serverId
     * @param  integer $siteId
     * @param  integer $certificateId
     * @return string
     */
    public function getCertificateSigningRequest($serverId, $siteId, $certificateId)
    {
        return $this->get("servers/$serverId/sites/$siteId/certificates/$certificateId/csr");
    }

    /**
     * Install the given certificate for the site.
     *
     * @param  integer $serverId
     * @param  integer $siteId
     * @param  integer $certificateId
     * @param  boolean $wait
     * @return void
     */
    public function installCertificate($serverId, $siteId, $certificateId, $wait = true)
    {
        $this->post("servers/$serverId/sites/$siteId/certificates/$certificateId/install");

        if ($wait) {
            $this->retry(30, function () use ($serverId, $siteId, $certificateId) {
                $certificate = $this->certificate($serverId, $siteId, $certificateId);

                return $certificate->status == 'installed';
            });
        }
    }

    /**
     * Activate the given certificate for the site.
     *
     * @param  integer $serverId
     * @param  integer $siteId
     * @param  integer $certificateId
     * @param  boolean $wait
     * @return void
     */
    public function activateCertificate($serverId, $siteId, $certificateId, $wait = true)
    {
        $this->post("servers/$serverId/sites/$siteId/certificates/$certificateId/activate");

        if ($wait) {
            $this->retry(30, function () use ($serverId, $siteId, $certificateId) {
                $certificate = $this->certificate($serverId, $siteId, $certificateId);

                return $certificate->activationStatus == 'activated';
            });
        }
    }
}