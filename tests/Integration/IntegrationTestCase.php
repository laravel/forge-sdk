<?php

declare(strict_types=1);

namespace Tests\Integration;

use GuzzleHttp\Client as HttpClient;
use Laravel\Forge\Forge;
use PHPUnit\Framework\TestCase;

abstract class IntegrationTestCase extends TestCase
{
    protected static ?Forge $forge = null;

    protected static string $organization = '';

    protected static ?int $serverId = null;

    protected static bool $skipped = false;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        if (static::$forge !== null) {
            return;
        }

        $envFile = __DIR__.'/.env.testing';

        if (! file_exists($envFile)) {
            static::$skipped = true;

            return;
        }

        $config = static::parseEnv($envFile);

        $token = $config['FORGE_API_TOKEN'] ?? '';
        $baseUri = $config['FORGE_API_URL'] ?? '';
        $organization = $config['FORGE_ORGANIZATION'] ?? '';

        if ($token === '' || $baseUri === '' || $organization === '') {
            static::$skipped = true;

            return;
        }

        $guzzle = new HttpClient([
            'base_uri' => $baseUri,
            'http_errors' => false,
            'verify' => false,
            'headers' => [
                'Authorization' => 'Bearer '.$token,
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
                'User-Agent' => 'Laravel Forge PHP/4.0 (Integration Tests)',
            ],
        ]);

        static::$forge = new Forge(null, $guzzle);
        static::$organization = $organization;

        $serverId = $config['FORGE_SERVER_ID'] ?? '';
        static::$serverId = $serverId !== '' ? (int) $serverId : null;
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (static::$skipped) {
            $this->markTestSkipped('Integration tests require a configured tests/Integration/.env.testing file.');
        }

        // Throttle requests to avoid hitting the API rate limiter.
        // The local Forge instance has a strict limit (~60 req/min).
        // Create-then-verify tests make multiple requests per test,
        // so we need a longer delay between test methods.
        sleep(2);
    }

    protected function forge(): Forge
    {
        return static::$forge;
    }

    protected function organization(): string
    {
        return static::$organization;
    }

    protected function serverId(): int
    {
        if (static::$serverId === null) {
            $this->markTestSkipped('This test requires FORGE_SERVER_ID in .env.testing.');
        }

        return static::$serverId;
    }

    private static function parseEnv(string $path): array
    {
        $config = [];

        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (str_contains($line, '=')) {
                [$key, $value] = explode('=', $line, 2);
                $config[trim($key)] = trim($value);
            }
        }

        return $config;
    }
}
