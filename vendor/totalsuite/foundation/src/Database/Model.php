<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Database;
! defined( 'ABSPATH' ) && exit();


use ArrayAccess;
use TotalRatingVendors\TotalSuite\Foundation\Contracts\Support\Arrayable;
use TotalRatingVendors\TotalSuite\Foundation\Database\Concerns\ModelAttributes;
use TotalRatingVendors\TotalSuite\Foundation\Http\Concerns\WithJsonResponse;

abstract class Model implements ArrayAccess, Arrayable
{
    use ModelAttributes, WithJsonResponse;

    /**
     * Model constructor.
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        if (!empty($attributes)) {
            $this->hydrate($attributes);
        }
    }

    /**
     * @param $attributes
     *
     * @return static
     */
    public static function fill($attributes)
    {
        $model = new static();
        $model->fillAttributes($attributes);

        return $model;
    }

    /**
     * @param $attributes
     * @param mixed ...$arguments
     * @return static
     */
    public static function from(...$arguments)
    {
        return new static(...$arguments);
    }

    /**
     * @param array $values
     * @param bool $cast
     *
     * @return static
     */
    public function hydrate(array $values, $cast = true)
    {
        if ($cast) {
            $values = $this->typeAttributes($values);
        }

        foreach ($values as $name => $value) {
            $this->setAttribute($name, $value);
        }

        return $this;
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return $this->hasAttribute($offset);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->getAttribute($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        $this->setAttribute($offset, $value);
    }

    /**
     * @param mixed $offset
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        $this->deleteAttribute($offset);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->attributes;
    }

    /**
     * @return array
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}