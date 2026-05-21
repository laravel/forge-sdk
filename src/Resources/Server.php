<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

use Laravel\Forge\CursorPaginator;

class Server extends Resource
{
    /**
     * The slug of the organization.
     */
    public string $organizationSlug;

    /**
     * The id of the server.
     */
    public ?int $id = null;

    /**
     * The name of the server.
     */
    public ?string $name = null;

    /**
     * The slug of the server.
     */
    public ?string $slug = null;

    /**
     * The type of the server.
     */
    public ?string $type = null;

    /**
     * The id of the provider credential instance.
     */
    public ?int $credentialId = null;

    /**
     * The size of the server.
     */
    public ?string $size = null;

    /**
     * The region of the server.
     */
    public ?string $region = null;

    /**
     * The IP address of the server.
     */
    public ?string $ipAddress = null;

    /**
     * The Private IP address of the server.
     */
    public ?string $privateIpAddress = null;

    /**
     * The PHP version used in the server.
     */
    public ?string $phpVersion = null;

    /**
     * Determine if the server installation is done.
     */
    public ?bool $isReady = null;

    /**
     * Determine if Forge access to the server was revoked.
     */
    public ?bool $revoked = null;

    /**
     * The date/time the server was created.
     */
    public ?string $createdAt = null;

    /**
     * The sudo password of the new server.
     */
    public ?string $sudoPassword = null;

    /**
     * The database password of the new server.
     */
    public ?string $databasePassword = null;

    /**
     * The provision command of the new server.
     */
    public ?string $provisionCommand = null;

    /**
     * The Ubuntu version of the server.
     */
    public ?string $ubuntuVersion = null;

    /**
     * The SSH port of the server.
     */
    public ?int $sshPort = null;

    /**
     * The provider of the server.
     */
    public ?string $provider = null;

    /**
     * The provider identifier of the server.
     */
    public ?string $identifier = null;

    /**
     * The PHP CLI version of the server.
     */
    public ?string $phpCliVersion = null;

    /**
     * The OPcache status of the server.
     */
    public ?string $opcacheStatus = null;

    /**
     * The database type of the server.
     */
    public ?string $databaseType = null;

    /**
     * The database status of the server.
     */
    public ?string $dbStatus = null;

    /**
     * The Redis status of the server.
     */
    public ?string $redisStatus = null;

    /**
     * The date/time the server was last updated.
     */
    public ?string $updatedAt = null;

    /**
     * The connection status of the server.
     */
    public ?string $connectionStatus = null;

    /**
     * The timezone of the server.
     */
    public ?string $timezone = null;

    /**
     * The local public key of the server.
     */
    public ?string $localPublicKey = null;

    /**
     * Delete the given server.
     */
    public function delete(): void
    {
        $this->forge->deleteServer($this->organizationSlug, $this->id);
    }

    /**
     * Reboot the server.
     */
    public function reboot(): void
    {
        $this->forge->createServerAction($this->organizationSlug, $this->id, ['action' => 'reboot']);
    }

    /**
     * Reboot MySQL on the server.
     */
    public function rebootMysql(): void
    {
        $this->forge->performMySQLAction($this->organizationSlug, $this->id, ['action' => 'restart']);
    }

    /**
     * Stop MySQL on the server.
     */
    public function stopMysql(): void
    {
        $this->forge->performMySQLAction($this->organizationSlug, $this->id, ['action' => 'stop']);
    }

    /**
     * Reboot Postgres on the server.
     */
    public function rebootPostgres(): void
    {
        $this->forge->performPostgresAction($this->organizationSlug, $this->id, ['action' => 'restart']);
    }

    /**
     * Stop Postgres on the server.
     */
    public function stopPostgres(): void
    {
        $this->forge->performPostgresAction($this->organizationSlug, $this->id, ['action' => 'stop']);
    }

    /**
     * Reboot Nginx on the server.
     */
    public function rebootNginx(): void
    {
        $this->forge->performNginxAction($this->organizationSlug, $this->id, ['action' => 'restart']);
    }

    /**
     * Stop Nginx on the server.
     */
    public function stopNginx(): void
    {
        $this->forge->performNginxAction($this->organizationSlug, $this->id, ['action' => 'stop']);
    }

    /**
     * Reboot PHP on the server.
     */
    public function rebootPHP(): void
    {
        $this->forge->performPHPAction($this->organizationSlug, $this->id, ['action' => 'restart']);
    }

    /**
     * Enable OPCache on the server.
     */
    public function enableOPCache(): void
    {
        $this->forge->createPhpOpcache($this->organizationSlug, $this->id, []);
    }

    /**
     * Disable OPCache on the server.
     */
    public function disableOPCache(): void
    {
        $this->forge->deletePhpOpcache($this->organizationSlug, $this->id);
    }

    /**
     * Get the collection of PHP Versions.
     */
    public function phpVersions(): CursorPaginator
    {
        return $this->forge->phpVersions($this->organizationSlug, $this->id);
    }

    /**
     * Install a version of PHP.
     */
    public function installPHP(string $version): void
    {
        $this->forge->installPhpVersion($this->organizationSlug, $this->id, ['version' => $version]);
    }

}
