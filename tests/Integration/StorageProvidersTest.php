<?php

declare(strict_types=1);

namespace Tests\Integration;

use Laravel\Forge\Resources\StorageProvider;

class StorageProvidersTest extends IntegrationTestCase
{
    private static ?int $testProviderId = null;

    /**
     * Create a storage provider for testing.
     */
    private function ensureTestProvider(): void
    {
        if (static::$testProviderId !== null) {
            return;
        }

        $provider = $this->forge()->createStorageProvider($this->organization(), [
            'provider' => 's3',
            'name' => 'SDK Test S3 '.time(),
            'region' => 'us-east-1',
            'bucket' => 'sdk-test-bucket',
            'access_key' => 'AKIAIOSFODNN7EXAMPLE',
            'secret_key' => 'wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY',
        ]);

        if ($provider->id === null || $provider->id === 0) {
            $this->markTestSkipped('Storage provider creation did not return a valid ID.');
        }

        static::$testProviderId = $provider->id;
    }

    public static function tearDownAfterClass(): void
    {
        if (static::$testProviderId !== null && static::$forge !== null) {
            try {
                static::$forge->deleteStorageProvider(static::$organization, static::$testProviderId);
            } catch (\Throwable) {
                // Best-effort cleanup.
            }
            static::$testProviderId = null;
        }

        parent::tearDownAfterClass();
    }

    public function test_list_storage_providers(): void
    {
        $this->ensureTestProvider();

        $providers = $this->forge()->storageProviders($this->organization());

        $this->assertIsArray($providers);
        $this->assertNotEmpty($providers);

        $provider = $providers[0];
        $this->assertInstanceOf(StorageProvider::class, $provider);
        $this->assertIsInt($provider->id);
        $this->assertIsString($provider->name);
        $this->assertNotEmpty($provider->name);
        $this->assertIsString($provider->createdAt, 'createdAt should be hydrated');

        // v2 properties
        $this->assertIsString($provider->provider, 'provider should be hydrated');
        $this->assertIsString($provider->providerName, 'providerName should be hydrated');
        $this->assertTrue(
            is_null($provider->region) || is_string($provider->region),
            'region should be null or string'
        );
        $this->assertTrue(
            is_null($provider->bucket) || is_string($provider->bucket),
            'bucket should be null or string'
        );
        $this->assertTrue(
            is_null($provider->inUse) || is_bool($provider->inUse),
            'inUse should be null or bool'
        );
        $this->assertTrue(
            is_null($provider->updatedAt) || is_string($provider->updatedAt),
            'updatedAt should be null or string'
        );

        // Envelope keys stripped
        $this->assertIsArray($provider->relationships);
        $this->assertIsArray($provider->links);
    }

    public function test_get_storage_provider(): void
    {
        $this->ensureTestProvider();

        $provider = $this->forge()->storageProvider($this->organization(), static::$testProviderId);

        $this->assertInstanceOf(StorageProvider::class, $provider);
        $this->assertSame(static::$testProviderId, $provider->id);
        $this->assertIsString($provider->name);
        $this->assertSame('s3', $provider->provider);
    }
}
