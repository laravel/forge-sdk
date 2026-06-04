<?php

declare(strict_types=1);

namespace Tests\Integration;

use Laravel\Forge\CursorPaginator;
use Laravel\Forge\Resources\SSHKey;

class SSHKeysTest extends IntegrationTestCase
{
    private static ?int $testKeyId = null;

    /**
     * Create an SSH key for testing and wait for it to be listed.
     */
    private function ensureTestKey(): void
    {
        if (static::$testKeyId !== null) {
            return;
        }

        $publicKey = 'ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIJEj1ZKj+X6iN2m8fHVoADwjMKiTrOGy3GqXBqFKbuh6 sdk-test@integration';

        $this->forge()->createSshKey($this->organization(), $this->serverId(), [
            'name' => 'SDK Test Key '.time(),
            'key' => $publicKey,
        ]);

        // Wait for the key to appear in the listing.
        sleep(3);

        $keys = $this->forge()->sshKeys($this->organization(), $this->serverId());

        foreach ($keys as $key) {
            if (str_starts_with($key->name, 'SDK Test Key')) {
                static::$testKeyId = $key->id;

                return;
            }
        }

        $this->markTestSkipped('SSH key creation was accepted but key did not appear in listing.');
    }

    public static function tearDownAfterClass(): void
    {
        if (static::$testKeyId !== null && static::$forge !== null) {
            try {
                static::$forge->deleteSshKey(static::$organization, static::$serverId, static::$testKeyId);
            } catch (\Throwable) {
                // Best-effort cleanup.
            }
            static::$testKeyId = null;
        }

        parent::tearDownAfterClass();
    }

    public function test_list_ssh_keys(): void
    {
        $this->ensureTestKey();

        $keys = $this->forge()->sshKeys($this->organization(), $this->serverId());

        $this->assertInstanceOf(CursorPaginator::class, $keys);
        $this->assertNotEmpty($keys);
        $this->assertContainsOnlyInstancesOf(SSHKey::class, $keys);

        $key = $keys[0];
        $this->assertInstanceOf(SSHKey::class, $key);
        $this->assertIsInt($key->id);
        $this->assertIsString($key->name);
        $this->assertNotEmpty($key->name);
        $this->assertIsString($key->status);
        $this->assertIsString($key->createdAt, 'createdAt should be hydrated');

        // v2 properties
        $this->assertTrue(
            is_null($key->user) || is_string($key->user),
            'user should be null or string'
        );
        $this->assertTrue(
            is_null($key->createdBy) || is_int($key->createdBy),
            'createdBy should be null or int'
        );
        $this->assertTrue(
            is_null($key->updatedAt) || is_string($key->updatedAt),
            'updatedAt should be null or string'
        );
    }

    public function test_ssh_key_has_no_jsonapi_envelope_keys(): void
    {
        $this->ensureTestKey();

        $keys = $this->forge()->sshKeys($this->organization(), $this->serverId());

        $this->assertNotEmpty($keys);

        $key = $keys[0];
        $this->assertIsArray($key->relationships);
        $this->assertIsArray($key->links);
    }
}
