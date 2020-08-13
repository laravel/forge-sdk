<?php

namespace Laravel\Forge\Resources;

use Laravel\Forge\Forge;

class Resource
{
    /**
     * The resource attributes.
     *
     * @var array
     */
    public $attributes;

    /**
     * The Forge SDK instance.
     *
     * @var Forge
     */
    protected $forge;

    /**
     * Create a new resource instance.
     *
     * @param  array $attributes
     * @param  Forge $forge
     * @return void
     */
    public function __construct(array $attributes, $forge = null)
    {
        $this->attributes = $attributes;
        $this->forge = $forge;

        $this->fill();
    }

    /**
     * Fill the resource with the array of attributes.
     *
     * @return void
     */
    private function fill()
    {
        foreach ($this->attributes as $key => $value) {
            $key = $this->camelCase($key);

            $this->{$key} = $value;
        }
    }

    /**
     * Convert the key name to camel case.
     *
     * @param $key
     */
    private function camelCase($key)
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
     *
     * @param  array $collection
     * @param  string $class
     * @param  array $extraData
     * @return array
     */
    protected function transformCollection($collection, $class, $extraData = [])
    {
        return array_map(function ($data) use ($class, $extraData) {
            return new $class($data + $extraData, $this->forge);
        }, $collection);
    }
}
