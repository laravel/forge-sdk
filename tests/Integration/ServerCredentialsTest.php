<?php

declare(strict_types=1);

namespace Tests\Integration;

use Laravel\Forge\Resources\ServerCredential;

class ServerCredentialsTest extends IntegrationTestCase
{
    public function test_list_server_credentials(): void
    {
        $credentials = $this->forge()->serverCredentials($this->organization());

        $this->assertIsArray($credentials);

        if (count($credentials) === 0) {
            $this->markTestSkipped('No server credentials available to test.');
        }

        $credential = $credentials[0];
        $this->assertInstanceOf(ServerCredential::class, $credential);
        $this->assertIsInt($credential->id);
        $this->assertIsString($credential->name);
        $this->assertNotEmpty($credential->name);
        $this->assertIsString($credential->createdAt, 'createdAt should be hydrated');

        // type is in the JSON:API envelope, not in attributes — may be null after stripping
        $this->assertTrue(
            is_null($credential->type) || is_string($credential->type),
            'type should be null or string'
        );

        // v2 properties
        $this->assertTrue(
            is_null($credential->provider) || is_string($credential->provider),
            'provider should be null or string'
        );
        $this->assertTrue(
            is_null($credential->inUse) || is_bool($credential->inUse),
            'inUse should be null or bool'
        );
        $this->assertTrue(
            is_null($credential->updatedAt) || is_string($credential->updatedAt),
            'updatedAt should be null or string'
        );
    }

    public function test_get_server_credential(): void
    {
        $credentials = $this->forge()->serverCredentials($this->organization());

        if (count($credentials) === 0) {
            $this->markTestSkipped('No server credentials available to test.');
        }

        $credential = $this->forge()->serverCredential($this->organization(), $credentials[0]->id);

        $this->assertInstanceOf(ServerCredential::class, $credential);
        $this->assertSame($credentials[0]->id, $credential->id);
        $this->assertIsString($credential->name);
    }

    public function test_server_credential_has_no_jsonapi_envelope_keys(): void
    {
        $credentials = $this->forge()->serverCredentials($this->organization());

        if (count($credentials) === 0) {
            $this->markTestSkipped('No server credentials available to test.');
        }

        $credential = $credentials[0];
        $this->assertArrayNotHasKey('relationships', $credential->attributes);
        $this->assertArrayNotHasKey('links', $credential->attributes);
    }
}
