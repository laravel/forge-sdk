<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

use Laravel\Forge\Forge;

/**
 * Base class for all hydrated Forge API resources.
 *
 * Hydration preserves the JSON:API `relationships`, `links`, and `included`
 * blocks as raw arrays on the resource. Use `included($name)` to resolve
 * relationship pointers against the top-level `included` document returned
 * when the response was fetched with an `include` query parameter.
 */
class Resource
{
    /**
     * The resource attributes.
     */
    public array $attributes;

    /**
     * The raw JSON:API `relationships` block, preserved as-is.
     */
    public array $relationships = [];

    /**
     * The raw JSON:API `links` block, preserved as-is.
     */
    public array $links = [];

    /**
     * The raw JSON:API `included` document, preserved as-is.
     */
    public array $included = [];

    /**
     * The Forge SDK instance.
     */
    protected ?Forge $forge = null;

    /**
     * Create a new resource instance.
     */
    public function __construct(array $attributes, ?Forge $forge = null, array $included = [])
    {
        $this->attributes = $attributes;
        $this->forge = $forge;
        $this->included = $included;

        $this->fill();
    }

    /**
     * Resolve the given relationship's pointers against the `included` document.
     */
    public function included(string $name): array
    {
        $pointers = $this->relationships[$name]['data'] ?? null;

        if (! is_array($pointers) || $pointers === []) {
            return [];
        }

        $isSingle = isset($pointers['type']);
        $pointers = $isSingle ? [$pointers] : $pointers;

        $index = [];

        foreach ($this->included as $item) {
            if (isset($item['type'], $item['id'])) {
                $index[$item['type'].':'.$item['id']] = $item;
            }
        }

        $matches = [];

        foreach ($pointers as $pointer) {
            if (! isset($pointer['type'], $pointer['id'])) {
                continue;
            }

            $key = $pointer['type'].':'.$pointer['id'];

            if (isset($index[$key])) {
                $matches[] = $index[$key];
            }
        }

        return $matches;
    }

    /**
     * Fill the resource with the array of attributes.
     */
    protected function fill(): void
    {
        // Flatten JSON:API response structure where properties
        // are nested under an "attributes" key.
        if (isset($this->attributes['attributes']) && is_array($this->attributes['attributes'])) {
            $nested = $this->attributes['attributes'];
            $hasAttributeType = array_key_exists('type', $nested);
            unset($this->attributes['attributes']);
            $this->attributes = array_merge($this->attributes, $nested);

            // Only strip the envelope "type" if it was NOT overwritten by
            // a "type" from within the attributes hash. Resources like
            // Monitor have a domain "type" (e.g. "disk") that must be kept.
            if (! $hasAttributeType) {
                unset($this->attributes['type']);
            }
        }

        foreach ($this->attributes as $key => $value) {
            $key = $this->camelCase($key);

            if (! property_exists($this, $key)) {
                continue;
            }

            $rp = new \ReflectionProperty($this, $key);

            // Skip null values for non-nullable typed properties (e.g. array).
            if (is_null($value)) {
                if ($rp->hasType() && ! $rp->getType()->allowsNull()) {
                    continue;
                }
            }

            // Coerce scalar types to match the declared property type.
            // JSON:API returns IDs as strings, but properties may be typed as int.
            if (! is_null($value) && $rp->hasType() && is_scalar($value)) {
                $type = $rp->getType();
                $typeName = $type instanceof \ReflectionNamedType ? $type->getName() : null;

                $value = match ($typeName) {
                    'int' => (int) $value,
                    'float' => (float) $value,
                    'string' => (string) $value,
                    'bool' => (bool) $value,
                    default => $value,
                };
            }

            $this->{$key} = $value;
        }
    }

    /**
     * Convert the key name to camel case.
     */
    protected function camelCase(string $key): string
    {
        $parts = explode('_', $key);

        foreach ($parts as $i => $part) {
            if ($i !== 0) {
                $parts[$i] = ucfirst($part);
            }
        }

        return str_replace(' ', '', implode(' ', $parts));
    }

    /**
     * Transform the items of the collection to the given class.
     */
    protected function transformCollection(array $collection, string $class, array $extraData = []): array
    {
        return array_map(function ($data) use ($class, $extraData) {
            return new $class($data + $extraData, $this->forge);
        }, $collection);
    }

}
