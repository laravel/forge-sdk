<?php

declare(strict_types=1);

/**
 * Debug script: hits the real Forge API v2 and captures raw JSON response structures.
 *
 * Usage: php tests/Integration/debug_api_shapes.php
 */

require __DIR__.'/../../vendor/autoload.php';

use GuzzleHttp\Client as HttpClient;

// ---------------------------------------------------------------------------
// 1. Read .env.testing credentials
// ---------------------------------------------------------------------------

$envFile = __DIR__.'/.env.testing';

if (! file_exists($envFile)) {
    fwrite(STDERR, "ERROR: {$envFile} not found.\n");
    exit(1);
}

$config = [];
foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    $line = trim($line);
    if ($line === '' || str_starts_with($line, '#')) {
        continue;
    }
    if (str_contains($line, '=')) {
        [$key, $value] = explode('=', $line, 2);
        $config[trim($key)] = trim($value);
    }
}

$baseUri      = $config['FORGE_API_URL']      ?? '';
$token        = $config['FORGE_API_TOKEN']     ?? '';
$organization = $config['FORGE_ORGANIZATION']  ?? '';
$serverId     = $config['FORGE_SERVER_ID']     ?? '';

if ($token === '' || $baseUri === '' || $organization === '') {
    fwrite(STDERR, "ERROR: Missing required env vars (FORGE_API_URL, FORGE_API_TOKEN, FORGE_ORGANIZATION).\n");
    exit(1);
}

echo "=== Forge API Shape Inspector ===\n";
echo "Base URI     : {$baseUri}\n";
echo "Organization : {$organization}\n";
echo "Server ID    : {$serverId}\n";
echo str_repeat('=', 80)."\n\n";

// ---------------------------------------------------------------------------
// 2. Create Guzzle client
// ---------------------------------------------------------------------------

$client = new HttpClient([
    'base_uri'    => $baseUri,
    'http_errors' => false,
    'verify'      => false,
    'headers'     => [
        'Authorization' => 'Bearer '.$token,
        'Accept'        => 'application/vnd.api+json',
        'Content-Type'  => 'application/vnd.api+json',
        'User-Agent'    => 'Forge SDK Debug Script',
    ],
]);

// ---------------------------------------------------------------------------
// 3. Helper functions
// ---------------------------------------------------------------------------

function printShape(string $label, string $endpoint, array $json, bool $isSingle): void
{
    echo str_repeat('-', 80)."\n";
    echo "RESOURCE : {$label}\n";
    echo "ENDPOINT : {$endpoint}\n";

    // Determine the item to inspect
    $data = $json['data'] ?? null;

    if ($data === null) {
        echo "  >> 'data' key is MISSING from response\n";
        echo "  >> Top-level keys: ".implode(', ', array_keys($json))."\n";
        echo "\n";
        return;
    }

    if ($isSingle) {
        $item = $data;
    } else {
        if (count($data) === 0) {
            echo "  >> Collection is EMPTY (no items to inspect)\n\n";
            return;
        }
        $item = $data[0];
    }

    // Top-level keys of the item
    echo "  ITEM TOP-LEVEL KEYS: ".implode(', ', array_keys($item))."\n";

    // id / type
    if (isset($item['id'])) {
        echo "  id   : ".var_export($item['id'], true)." (".gettype($item['id']).")\n";
    }
    if (isset($item['type'])) {
        echo "  type : ".var_export($item['type'], true)."\n";
    }

    // Attributes
    if (isset($item['attributes']) && is_array($item['attributes'])) {
        echo "  ATTRIBUTES (".count($item['attributes'])." keys):\n";
        foreach ($item['attributes'] as $key => $value) {
            $type = gettype($value);
            $preview = '';
            if (is_null($value)) {
                $preview = 'null';
            } elseif (is_bool($value)) {
                $preview = $value ? 'true' : 'false';
            } elseif (is_string($value)) {
                $preview = '"'.mb_substr($value, 0, 80).(mb_strlen($value) > 80 ? '...' : '').'"';
            } elseif (is_int($value) || is_float($value)) {
                $preview = (string) $value;
            } elseif (is_array($value)) {
                $preview = json_encode($value, JSON_UNESCAPED_SLASHES);
                if (strlen($preview) > 120) {
                    $preview = mb_substr($preview, 0, 120).'...';
                }
            }
            echo "    - {$key} : {$type} = {$preview}\n";
        }
    } else {
        echo "  ATTRIBUTES: (none / not present)\n";
    }

    // Relationships
    if (isset($item['relationships']) && is_array($item['relationships'])) {
        echo "  RELATIONSHIPS (".count($item['relationships'])." keys):\n";
        foreach ($item['relationships'] as $relName => $relData) {
            $relKeys = is_array($relData) ? implode(', ', array_keys($relData)) : '(not array)';
            // Check if the relationship data is a collection or single
            if (isset($relData['data'])) {
                if (is_array($relData['data']) && isset($relData['data'][0])) {
                    $count = count($relData['data']);
                    echo "    - {$relName} : collection ({$count} items) [keys: {$relKeys}]\n";
                } elseif (is_array($relData['data']) && isset($relData['data']['type'])) {
                    echo "    - {$relName} : single ({$relData['data']['type']}#{$relData['data']['id']}) [keys: {$relKeys}]\n";
                } elseif ($relData['data'] === null) {
                    echo "    - {$relName} : null [keys: {$relKeys}]\n";
                } else {
                    echo "    - {$relName} : [keys: {$relKeys}]\n";
                }
            } else {
                echo "    - {$relName} : [keys: {$relKeys}]\n";
            }
        }
    } else {
        echo "  RELATIONSHIPS: (none / not present)\n";
    }

    // Links
    if (isset($item['links']) && is_array($item['links'])) {
        echo "  LINKS: ".implode(', ', array_keys($item['links']))."\n";
    }

    // Meta
    if (isset($item['meta']) && is_array($item['meta'])) {
        echo "  META: ".implode(', ', array_keys($item['meta']))."\n";
    }

    // Top-level meta/links on the response itself
    if (isset($json['meta'])) {
        echo "  RESPONSE META: ".json_encode($json['meta'], JSON_UNESCAPED_SLASHES)."\n";
    }
    if (isset($json['links'])) {
        echo "  RESPONSE LINKS: ".json_encode($json['links'], JSON_UNESCAPED_SLASHES)."\n";
    }

    echo "\n";
}

function fetchAndPrint(HttpClient $client, string $label, string $endpoint, bool $isSingle): ?array
{
    echo "[GET] {$endpoint} ...";
    $response = $client->get($endpoint);
    $status = $response->getStatusCode();
    echo " HTTP {$status}\n";

    if ($status >= 400) {
        $body = (string) $response->getBody();
        echo "  >> ERROR: HTTP {$status}\n";
        $preview = mb_substr($body, 0, 500);
        echo "  >> Body: {$preview}\n\n";
        return null;
    }

    $body = (string) $response->getBody();
    $json = json_decode($body, true);

    if (! is_array($json)) {
        echo "  >> Could not decode JSON\n";
        echo "  >> Raw: ".mb_substr($body, 0, 500)."\n\n";
        return null;
    }

    printShape($label, $endpoint, $json, $isSingle);

    return $json;
}

// ---------------------------------------------------------------------------
// 4. Hit each endpoint
// ---------------------------------------------------------------------------

$org = $organization;
$sid = $serverId;

// User (single)
fetchAndPrint($client, 'User', 'user', true);
sleep(1);

// Organizations (collection)
fetchAndPrint($client, 'Organizations', 'orgs', false);
sleep(1);

// Organization (single)
fetchAndPrint($client, 'Organization (single)', "orgs/{$org}", true);
sleep(1);

// Servers (collection)
fetchAndPrint($client, 'Servers', "orgs/{$org}/servers", false);
sleep(1);

// Server (single)
fetchAndPrint($client, 'Server (single)', "orgs/{$org}/servers/{$sid}", true);
sleep(1);

// Sites (collection)
fetchAndPrint($client, 'Sites', "orgs/{$org}/servers/{$sid}/sites", false);
sleep(1);

// Databases (collection)
$dbResponse = fetchAndPrint($client, 'Databases', "orgs/{$org}/servers/{$sid}/databases", false);
sleep(1);

// Database (single) - use first DB if available
if ($dbResponse && isset($dbResponse['data'][0]['id'])) {
    $dbId = $dbResponse['data'][0]['id'];
    fetchAndPrint($client, 'Database (single)', "orgs/{$org}/servers/{$sid}/databases/{$dbId}", true);
    sleep(1);
} else {
    echo "[SKIP] Database (single) - no databases found\n\n";
}

// SSH Keys (collection)
fetchAndPrint($client, 'SSH Keys', "orgs/{$org}/servers/{$sid}/ssh-keys", false);
sleep(1);

// Monitors (collection)
fetchAndPrint($client, 'Monitors', "orgs/{$org}/servers/{$sid}/monitors", false);
sleep(1);

// Firewall Rules (collection)
fetchAndPrint($client, 'Firewall Rules', "orgs/{$org}/servers/{$sid}/firewall-rules", false);
sleep(1);

// Scheduled Jobs (collection)
fetchAndPrint($client, 'Scheduled Jobs', "orgs/{$org}/servers/{$sid}/scheduled-jobs", false);
sleep(1);

// Events (collection)
fetchAndPrint($client, 'Events', "orgs/{$org}/servers/{$sid}/events", false);
sleep(1);

// PHP Versions (collection)
fetchAndPrint($client, 'PHP Versions', "orgs/{$org}/servers/{$sid}/php-versions", false);
sleep(1);

// Nginx Templates (collection)
fetchAndPrint($client, 'Nginx Templates', "orgs/{$org}/servers/{$sid}/nginx-templates", false);
sleep(1);

// Security Rules (collection)
fetchAndPrint($client, 'Security Rules', "orgs/{$org}/servers/{$sid}/security-rules", false);
sleep(1);

// Recipes (collection)
fetchAndPrint($client, 'Recipes', "orgs/{$org}/recipes", false);
sleep(1);

// Server Credentials (collection)
fetchAndPrint($client, 'Server Credentials', "orgs/{$org}/server-credentials", false);
sleep(1);

// Storage Providers (collection)
fetchAndPrint($client, 'Storage Providers', "orgs/{$org}/storage-providers", false);
sleep(1);

// Teams (collection)
fetchAndPrint($client, 'Teams', "orgs/{$org}/teams", false);
sleep(1);

// Roles (collection)
fetchAndPrint($client, 'Roles', "orgs/{$org}/roles", false);
sleep(1);

// Permissions (collection)
fetchAndPrint($client, 'Permissions', 'permissions', false);
sleep(1);

// Predefined Roles (collection)
fetchAndPrint($client, 'Predefined Roles', 'predefined-roles', false);
sleep(1);

// Providers (collection)
$providersResponse = fetchAndPrint($client, 'Providers', 'providers', false);
sleep(1);

// Regions & Sizes (use first provider)
if ($providersResponse && isset($providersResponse['data'][0])) {
    $firstProvider = $providersResponse['data'][0];
    $providerSlug = $firstProvider['attributes']['slug'] ?? $firstProvider['id'] ?? null;

    if ($providerSlug) {
        fetchAndPrint($client, 'Regions', "providers/{$providerSlug}/regions", false);
        sleep(1);

        fetchAndPrint($client, 'Sizes', "providers/{$providerSlug}/sizes", false);
        sleep(1);
    } else {
        echo "[SKIP] Regions/Sizes - could not determine provider slug\n";
    }
} else {
    echo "[SKIP] Regions/Sizes - no providers found\n";
}

echo str_repeat('=', 80)."\n";
echo "DONE.\n";
