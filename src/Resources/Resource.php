<?php

namespace Themsaid\Forge\Resources;

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
}