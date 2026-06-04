<?php

declare(strict_types=1);

namespace Laravel\Forge\Resources;

use Laravel\Forge\Forge;

/**
 * Base class for all hydrated Forge API resources.
 *
 * Hydration preserves the JSON:API `relationships` and `links` blocks as raw
 * arrays on the resource (matching their original JSON:API shape) so callers
 * can follow IDs and sub-resource links without re-parsing attributes or making
 * redundant API calls. Note that the top-level `included` document is NOT
 * auto-resolved: the SDK only consumes the `data` envelope from responses, so
 * any references in `relationships` remain as identifier pointers rather than
 * fully-hydrated child resources.
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
     * The Forge SDK instance.
     */
    protected ?Forge $forge = null;

    /**
     * Create a new resource instance.
     */
    public function __construct(array $attributes, ?Forge $forge = null)
    {
        $this->attributes = $attributes;
        $this->forge = $forge;

        $this->fill();
    }

    /**
     * Fill the resource with the array of attributes.
     *
     * `relationships` and `links` from a JSON:API payload are assigned to the
     * matching public properties via the camelCase + `property_exists` loop
     * below, preserving the raw JSON:API shape. The top-level `included`
     * document is not consumed (the SDK only sees the `data` envelope), so
     * relationship references remain unresolved identifier pointers.
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
