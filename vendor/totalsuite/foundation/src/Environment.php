<?php

namespace TotalRatingVendors\TotalSuite\Foundation;
! defined( 'ABSPATH' ) && exit();


use ArrayAccess;
use TotalRatingVendors\TotalSuite\Foundation\Contracts\Support\Arrayable;
use TotalRatingVendors\TotalSuite\Foundation\Helpers\Concerns\ResolveFromContainer;
use TotalRatingVendors\TotalSuite\Foundation\Support\Arrays;

/**
 * Class Environment
 *
 * @package TotalSuite\Foundation
 */
class Environment implements Arrayable, ArrayAccess
{
    use ResolveFromContainer;

    /**
     * @var array $items
     */
    protected $items;

    /**
     * Environment constructor.
     *
     * @param $items
     */
    public function __construct($items)
    {
        $this->items = is_array($items) ? $items : [];
    }

    /**
     * Get items as array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return Arrays::has($this->items, $offset);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Get item.
     *
     * @param      $key
     * @param null $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arrays::get($this->items, $key, $default);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * Set item.
     *
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        Arrays::set($this->items, $key, $value);
    }

    /**
     * @param mixed $offset
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        $this->set($offset, null);
    }

    /**
     * @return array|mixed
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->items;
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * @param $key
     * @param $value
     *
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return (bool)$this->get($key);
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return defined('WP_DEBUG') && WP_DEBUG;
    }

    /**
     * @param       $context
     * @param mixed $value
     *
     * @return bool
     */
    public function is($context, $value = true): bool
    {
        return $this->get($context) === $value;
    }

    /**
     * @return bool
     */
    public function isAjax(): bool
    {
        return defined('DOING_AJAX') && DOING_AJAX;
    }

    /**
     * @return string
     */
    public function hostName(): string
    {
        return parse_url(get_site_url(), PHP_URL_HOST);
    }

    /**
     * @return bool
     */
    public function isRest(): bool
    {
        return defined('REST_REQUEST');
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return is_admin();
    }

    /**
     * @return bool
     */
    public function isProduction(): bool
    {
        return !$this->isDebug();
    }

    /**
     * @return bool
     */
    public function isPrettyPermalinks(): bool
    {
        return (bool)get_option('permalink_structure');
    }
}