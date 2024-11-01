<?php

namespace TotalRating\Entities;
! defined( 'ABSPATH' ) && exit();


use ArrayAccess;
use TotalRatingVendors\TotalSuite\Foundation\Contracts\Support\Arrayable;
use TotalRatingVendors\TotalSuite\Foundation\Support\Arrays;

class Entity implements Arrayable, ArrayAccess
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $typeName;

    /**
     * @var string
     */
    protected $url = '';

    /**
     * @var array
     */
    protected $meta = [];

    /**
     * constructor.
     *
     * @param  mixed  $id
     * @param  string  $title
     * @param  string  $type
     * @param        $typeName
     * @param  string  $url
     * @param  array  $meta
     */
    public function __construct($id, $title, $type, $typeName, $url, array $meta = [])
    {
        $this->id       = $id;
        $this->name     = $title;
        $this->type     = $type;
        $this->url      = $url;
        $this->meta     = $meta;
        $this->typeName = $typeName;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param  null  $key
     * @param  null  $default
     *
     * @return array
     */
    public function getMeta($key = null, $default = null)
    {
        if ($key === null) {
            return $this->meta;
        }

        return Arrays::get($this->meta, $key, $default);
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return isset($this->{$offset});
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->{$offset} ?? null;
    }

    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        $this->{$offset} = $value;
    }

    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        unset($this->{$offset});
    }

    public function getUid()
    {
        return md5(serialize($this));
    }
}
