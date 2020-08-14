<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Certificate;

trait ManagesCertificates
{
    /**
     * Get the collection of certificates.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @return \Laravel\Forge\Resources\Certificate[]
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
     * @param  int  $serverId
     * @param  int  $siteId
     * @param  int  $certificateId
     * @return \Laravel\Forge\Resources\Certificate
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
     * @param  int  $serverId
     * @param  int  $siteId
     * @param  array  $data
     * @param  bool  $wait
     * @return \Laravel\Forge\Resources\Certificate
     */
    public function createCertificate($serverId, $siteId, array $data, $wait = true)
    {
        $certificate = $this->post("servers/$serverId/sites/$siteId/certificates", $data)['certificate'];

        if ($wait) {
            return $this->retry($this->getTimeout(), function () use ($serverId, $siteId, $certificate) {
                $certificate = $this->certificate($serverId, $siteId, $certificate['id']);

                return $certificate->status == 'installed' ? $certificate : null;
            });
        }

        return new Certificate($certificate + ['server_id' => $serverId, 'site_id' => $siteId], $this);
    }

    /**
     * Delete the given certificate.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @param  int  $certificateId
     * @return void
     */
    public function deleteCertificate($serverId, $siteId, $certificateId)
    {
        $this->delete("servers/$serverId/sites/$siteId/certificates/$certificateId");
    }

    /**
     * Get the SSL certificate signing request for the site.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @param  int  $certificateId
     * @return string
     */
    public function getCertificateSigningRequest($serverId, $siteId, $certificateId)
    {
        return $this->get("servers/$serverId/sites/$siteId/certificates/$certificateId/csr");
    }

    /**
     * Install the given certificate for the site.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @param  int  $certificateId
     * @param  array  $data
     * @param  bool  $wait
     * @return void
     */
    public function installCertificate($serverId, $siteId, $certificateId, array $data, $wait = true)
    {
        $this->post("servers/$serverId/sites/$siteId/certificates/$certificateId/install", $data);

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
     * @param  int  $serverId
     * @param  int  $siteId
     * @param  int  $certificateId
     * @param  bool  $wait
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

    /**
     * Add a LetsEncrypt certificate to a given site.
     *
     * @param  int  $serverId
     * @param  int  $siteId
     * @param  array  $data
     * @param  bool  $wait
     * @return \Laravel\Forge\Resources\Certificate
     */
    public function obtainLetsEncryptCertificate($serverId, $siteId, array $data, $wait = true)
    {
        $certificate = $this->post("servers/$serverId/sites/$siteId/certificates/letsencrypt", $data)['certificate'];

        if ($wait) {
            return $this->retry($this->getTimeout(), function () use ($serverId, $siteId, $certificate) {
                $certificate = $this->certificate($serverId, $siteId, $certificate['id']);

                return $certificate->status == 'installed' ? $certificate : null;
            });
        }

        return new Certificate($certificate + ['server_id' => $serverId, 'site_id' => $siteId], $this);
    }
}
