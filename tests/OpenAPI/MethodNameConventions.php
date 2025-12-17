<?php

namespace Tests\OpenAPI;

/**
 * Naming conventions for mapping API endpoints to SDK method names.
 *
 * This class defines the patterns and rules for automatically inferring
 * SDK method names from API endpoint paths.
 */
class MethodNameConventions
{
    /**
     * Resource prefixes based on scope.
     */
    protected static array $scopePrefixes = [
        'servers' => 'server',
        'sites' => 'site',
        'teams' => 'team',
        'organizations' => 'organization',
    ];

    /**
     * Resources that should be prefixed with their scope.
     */
    protected static array $scopedResources = [
        'events', 'logs', 'key', 'keys',
    ];

    /**
     * Service action endpoints.
     */
    protected static array $services = [
        'nginx', 'postgres', 'redis', 'mysql', 'php', 'supervisor',
    ];

    /**
     * Namespace/module prefixes that should be combined with resource names.
     * These are path segments that act as namespaces for other resources.
     * Example: /database/schemas -> database is the namespace, schemas is the resource
     */
    protected static array $namespaces = [
        'php', 'nginx', 'database', 'composer',
    ];

    /**
     * Special resource name transformations.
     */
    protected static array $resourceTransforms = [
        'orgs' => 'organizations',
        'archives' => 'archivedServers',
        'schemas' => 'databases',
        'invites' => 'invitations',
        'server-credentials' => 'serverCredentials',
        'background-processes' => 'backgroundProcesses',
        'firewall-rules' => 'firewallRules',
        'scheduled-jobs' => 'scheduledJobs',
    ];

    /**
     * Integration names that map to get{Name} method pattern.
     */
    protected static array $integrations = [
        'horizon', 'octane', 'reverb', 'inertia', 'pulse',
        'laravel-maintenance' => 'Maintenance',
        'laravel-scheduler' => 'Scheduler',
    ];

    /**
     * Map an endpoint to a method name using conventions.
     */
    public static function mapToMethodName(string $httpMethod, string $path): ?string
    {
        $httpMethod = strtoupper($httpMethod);
        $segments = array_filter(explode('/', trim($path, '/')), 'strlen');

        // Extract parameter segments and non-parameter segments
        $nonParamSegments = array_filter($segments, fn ($s) => ! str_starts_with($s, '{'));
        $allSegments = $segments;

        // Remove common base segments
        $nonParamSegments = array_values(array_filter($nonParamSegments, fn ($s) => ! in_array($s, ['orgs', 'api', 'v1', 'v2'])));

        if (empty($nonParamSegments)) {
            return null;
        }

        // Check for special patterns
        if ($method = static::checkSpecialPatterns($httpMethod, $allSegments, $nonParamSegments)) {
            return $method;
        }

        // Build method name using conventions
        return static::buildMethodName($httpMethod, $allSegments, $nonParamSegments);
    }

    /**
     * Check for special endpoint patterns.
     */
    protected static function checkSpecialPatterns(string $httpMethod, array $allSegments, array $nonParamSegments): ?string
    {
        $path = implode('/', $nonParamSegments);

        // Service actions: /services/{service}/actions -> perform{Service}Action()
        if (str_contains($path, 'services/') && end($nonParamSegments) === 'actions') {
            $serviceIndex = array_search('services', $nonParamSegments);
            $service = $allSegments[array_keys($allSegments)[array_search('services', $nonParamSegments) + 1]] ?? null;
            if ($service && str_starts_with($service, '{')) {
                $service = trim($service, '{}');
                $service = static::pascalCase($service);

                return 'perform'.$service.'Action';
            }
        }

        // Background process actions: /background-processes/{process}/actions
        if (str_contains($path, 'background-processes') && end($nonParamSegments) === 'actions') {
            return 'performBackgroundProcessAction';
        }

        // Server/Domain/Certificate actions
        if (end($nonParamSegments) === 'actions' && $httpMethod === 'POST') {
            if (str_contains($path, 'servers') && ! str_contains($path, 'sites')) {
                return 'createServerAction';
            }
            if (str_contains($path, 'domains')) {
                return 'createDomainAction';
            }
            if (str_contains($path, 'certificate')) {
                return 'createDomainCertificateAction';
            }
        }

        // Integrations: /integrations/{name} -> get{Name}(), create{Name}(), delete{Name}()
        if (in_array('integrations', $nonParamSegments)) {
            $integrationIndex = array_search('integrations', $allSegments);
            $integration = $allSegments[$integrationIndex + 1] ?? null;

            if ($integration && ! str_starts_with($integration, '{')) {
                $name = static::$integrations[$integration] ?? static::pascalCase($integration);

                return match ($httpMethod) {
                    'GET' => 'get'.$name,
                    'POST' => 'create'.$name,
                    'DELETE' => 'delete'.$name,
                    default => null,
                };
            }
        }

        // Team sharing: /teams/{team}/servers|recipes|server-credentials
        if (in_array('teams', $nonParamSegments)) {
            $last = end($nonParamSegments);
            if (in_array($last, ['servers', 'recipes', 'server-credentials'])) {
                $resource = static::pascalCase($last);

                return match ($httpMethod) {
                    'GET' => 'team'.$resource,
                    'POST' => 'createTeam'.$resource.'Share',
                    'DELETE' => 'deleteTeam'.$resource.'Share',
                    default => null,
                };
            }
        }

        return null;
    }

    /**
     * Build method name from segments using conventions.
     */
    protected static function buildMethodName(string $httpMethod, array $allSegments, array $nonParamSegments): string
    {
        $last = end($nonParamSegments);
        $isSingular = end($allSegments) !== $last;

        // Transform resource name if needed
        $resourceName = static::$resourceTransforms[$last] ?? $last;

        // Check if resource needs scope prefix
        $scopePrefix = static::getScopePrefix($nonParamSegments, $resourceName);

        // Check if resource needs namespace prefix
        $namespacePrefix = static::getNamespacePrefix($nonParamSegments);

        // Build the full name with proper casing
        if ($namespacePrefix) {
            // For namespaced resources: php + CliVersion -> phpCliVersion
            $fullName = $namespacePrefix.static::pascalCase($resourceName);
        } elseif ($scopePrefix) {
            // For scoped resources: server + Events -> serverEvents
            $fullName = $scopePrefix.static::pascalCase($resourceName);
        } else {
            // For normal resources: just camelCase
            $fullName = static::camelCase($resourceName);
        }

        // Apply HTTP method convention
        if ($isSingular) {
            $fullName = static::singularize($fullName);
        }

        return match ($httpMethod) {
            'GET' => $fullName,
            'POST' => 'create'.static::pascalCase(static::singularize($fullName)),
            'PUT', 'PATCH' => 'update'.static::pascalCase(static::singularize($fullName)),
            'DELETE' => 'delete'.static::pascalCase(static::singularize($fullName)),
            default => $fullName,
        };
    }

    /**
     * Get scope prefix if resource should be scoped.
     */
    protected static function getScopePrefix(array $nonParamSegments, string $resourceName): string
    {
        // Only apply scope prefix to certain resources
        if (! in_array($resourceName, static::$scopedResources) && ! in_array(static::singularize($resourceName), static::$scopedResources)) {
            return '';
        }

        foreach (static::$scopePrefixes as $scope => $prefix) {
            if (in_array($scope, $nonParamSegments)) {
                return $prefix;
            }
        }

        return '';
    }

    /**
     * Get namespace prefix if resource is namespaced.
     * Returns in camelCase but ready to be prepended to a PascalCase resource.
     */
    protected static function getNamespacePrefix(array $nonParamSegments): string
    {
        foreach (static::$namespaces as $namespace) {
            if (in_array($namespace, $nonParamSegments)) {
                // Return as lowercase for proper camelCase combination
                return strtolower(str_replace('-', '', $namespace));
            }
        }

        return '';
    }

    /**
     * Singularize a resource name.
     */
    protected static function singularize(string $word): string
    {
        $irregulars = [
            'organizations' => 'organization',
            'servers' => 'server',
            'sites' => 'site',
            'databases' => 'database',
            'archivedServers' => 'archivedServer',
            'serverCredentials' => 'serverCredential',
            'invitations' => 'invitation',
            'phpVersions' => 'phpVersion',
            'nginxTemplates' => 'nginxTemplate',
            'backgroundProcesses' => 'backgroundProcess',
            'firewallRules' => 'firewallRule',
            'scheduledJobs' => 'scheduledJob',
            'roles' => 'role',
            'recipes' => 'recipe',
        ];

        if (isset($irregulars[$word])) {
            return $irregulars[$word];
        }

        if (str_ends_with($word, 'ies')) {
            return substr($word, 0, -3).'y';
        }

        // Don't singularize words ending in 'ess' or 'cess' or 'ss'
        if (str_ends_with($word, 'ss') || str_ends_with($word, 'ess') || str_ends_with($word, 'cess')) {
            return $word;
        }

        if (str_ends_with($word, 'es')) {
            return substr($word, 0, -2);
        }

        if (str_ends_with($word, 's')) {
            return substr($word, 0, -1);
        }

        return $word;
    }

    /**
     * Convert to camelCase.
     */
    protected static function camelCase(string $string): string
    {
        return lcfirst(static::pascalCase($string));
    }

    /**
     * Convert to PascalCase.
     */
    protected static function pascalCase(string $string): string
    {
        return str_replace([' ', '-', '_'], '', ucwords(str_replace(['-', '_'], ' ', $string)));
    }
}
