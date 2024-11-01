<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Helpers\Concerns;
! defined( 'ABSPATH' ) && exit();



/**
 * Trait Attributes
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Helpers\Concerns
 */
trait Attributes
{
    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     * @param bool  $merge
     *
     * @return $this
     */
    public function setAttributes(array $attributes, $merge = false)
    {
        foreach ($attributes as $name => $attribute) {
            $this->setAttribute($name, $attribute, $merge);
        }

        return $this;
    }

    /**
     * @param string $name
     * @param mixed  $value
     * @param bool   $merge
     *
     * @return static
     */
    public function setAttribute($name, $value = null, $merge = false)
    {
        if (func_num_args() === 1) {
            $this->attributes[] = [$name];
        } elseif ($merge && $this->hasAttribute($name)) {
            $this->attributes[$name] = array_merge($this->attributes[$name], (array)$value);
        } else {
            $this->attributes[$name] = (array)$value;
        }

        return $this;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function hasAttribute($name)
    {
        return isset($this->attributes[$name]);
    }

    /**
     * @param $name
     *
     * @return mixed|null
     */
    public function __get($name)
    {
        return $this->getAttribute($name);
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->setAttribute($name, $value);
    }

    /**
     * @param      $name
     * @param null $default
     *
     * @return mixed|null
     */
    public function getAttribute($name, $default = null)
    {
        return $this->hasAttribute($name) ? $this->attributes[$name] : $default;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return $this->hasAttribute($name);
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function removeAttribute($name)
    {
        if (array_key_exists($name, $this->attributes)) {
            unset($this->attributes[$name]);
            return true;
        }

        return false;
    }
}