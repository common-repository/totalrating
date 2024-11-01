<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Support;
! defined( 'ABSPATH' ) && exit();


use ArrayAccess;

/**
 * Class Arrays
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Support
 */
class Arrays
{
    /**
     * @param array $array
     * @param int $depth
     *
     * @return array
     */
    public static function flatten($array, $depth = INF)
    {
        $result = [];

        foreach ($array as $item) {
            $item = $item instanceof Collection ? $item->all() : $item;

            if (!static::isArray($item)) {
                $result[] = $item;
            } else {
                $values = $depth === 1 ? array_values($item) : static::flatten($item, $depth - 1);

                foreach ($values as $value) {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * @param array $array
     * @param mixed $value
     * @param mixed|null $key
     *
     * @return mixed
     */
    public static function extract($array, $value, $key = null)
    {
        $items = [];

        foreach ($array as $item) {
            $valueData = static::get($item, $value);

            if ($key === null && $valueData !== null) {
                $items[] = $valueData;
            } else {
                $keyData = static::get($item, $key);

                if ($keyData !== null) {
                    $items[$keyData] = $valueData;
                }
            }
        }

        return $items;
    }

    /**
     * Get an item from an array using "dot" notation.
     *
     * @param      $array
     * @param      $key
     * @param null $default
     *
     * @return mixed
     */
    public static function get($array, $key, $default = null)
    {
        if (!static::isArray($array)) {
            return $default;
        }

        if (static::exists($array, $key)) {
            return $array[$key];
        }

        if (strpos($key, '.') === false) {
            return $default;
        }

        foreach (explode('.', $key) as $segment) {
            if (static::isArray($array) && static::exists($array, $segment)) {
                $array = $array[$segment];
                continue;
            }

            return $default;
        }

        return $array;
    }

    /**
     * Check if an item exists in the given array.
     *
     * @param array|ArrayAccess $array
     * @param mixed $key
     *
     * @return bool
     */
    public static function exists($array, $key): bool
    {
        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }

        return array_key_exists($key, $array);
    }

    /**
     * Get an item from an array using "dot" notation before remove it.
     *
     * @param array $array
     * @param       $key
     * @param null $default
     *
     * @return mixed|null
     */
    public static function pull(array &$array, $key, $default = null)
    {
        if (static::has($array, $key)) {
            $value = static::get($array, $key, $default);
            static::delete($array, $key);
            $array = array_merge($array, []);

            return $value;
        }

        return $default;
    }

    /**
     * Check if an item exists in the given array using Dot notation.
     *
     * @param       $array
     * @param mixed $key
     *
     * @return bool
     */
    public static function has($array, $key): bool
    {
        if (strpos($key, '.') === false) {
            return static::exists($array, $key);
        }

        foreach (explode('.', $key) as $segment) {
            if (!static::isArray($array) || !static::exists($array, $segment)) {
                return false;
            }

            $array = $array[$segment];
        }

        return true;
    }

    /**
     * Delete an item from an array using "dot" notation.
     *
     * @param $array
     * @param $key
     *
     * @return bool
     */
    public static function delete(array &$array, $key): bool
    {
        if ($key === null || !static::has($array, $key)) {
            return false;
        }

        $segments = explode('.', $key);
        $key = array_pop($segments);

        foreach ($segments as $segment) {
            if (static::exists($array, $segment) && static::isArray($array[$segment])) {
                $array = &$array[$segment];
            } else {
                return false;
            }
        }

        unset($array[$key]);

        return true;
    }

    /**
     * Merge multiple array.
     *
     * @param       $array
     * @param mixed ...$arrays
     *
     * @return array
     */
    public static function merge($array, ...$arrays): array
    {
        return array_merge($array, ...$arrays);
    }

    /**
     * Merge multiple array recursively.
     *
     * @param       $array
     * @param mixed ...$arrays
     *
     * @return array
     */
    public static function mergeRecursive($array, ...$arrays): array
    {
        return array_merge_recursive($array, ...$arrays);
    }

    /**
     * Get all items except the specified keys
     *
     * @param array $array
     * @param array $keys
     *
     * @return array
     */
    public static function except($array, array $keys): array
    {
        foreach ($keys as $key) {
            static::delete($array, $key);
        }

        return $array;
    }

    /**
     * Get only items specified keys
     *
     * @param array $array
     * @param array $keys
     *
     * @return array
     */
    public static function only($array, array $keys): array
    {
        $results = [];

        foreach ($keys as $key) {
            if (static::has($array, $key)) {
                $value = static::get($array, $key);
                static::set($results, $key, $value);
            }
        }

        return $results;
    }

    /**
     * Set an item in array using "dot" notation.
     *
     * @param array $array
     * @param mixed $key
     * @param       $value
     */
    public static function set(array &$array, $key, $value)
    {
        if ($key === null) {
            return;
        }

        $segments = explode('.', $key);
        $key = array_pop($segments);

        foreach ($segments as $segment) {
            if (!static::exists($array, $segment) || !static::isArray($array[$segment])) {
                $array[$segment] = [];
            }

            $array = &$array[$segment];
        }

        $array[$key] = $value;
    }

    /**
     * Append an item to the specified key
     *
     * @param array $array
     * @param       $key
     * @param       $value
     *
     * @return array
     */
    public static function prepend($array, $key, $value): array
    {
        if (!static::has($array, $key)) {
            static::set($array, $key, [$value]);
        }

        $values = (array)static::get($array, $key);
        array_unshift($values, $value);

        static::set($array, $key, $values);

        return $array;
    }

    /**
     * Check if a value exists in the array
     *
     * @param array $array
     * @param       $search
     * @param bool $strict
     *
     * @return bool
     */
    public static function find($array, $search, $strict = false): bool
    {
        if (empty($array)) {
            return false;
        }

        foreach ($array as $value) {
            if (static::isArray($value)) {
                $check = static::find($value, $search, $strict);
            } else {
                $check = $strict ? $search === $value : $search == $value;
            }

            if ($check) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $array
     * @param callable|null $callback
     * @param null $default
     *
     * @return mixed|null
     */
    public static function first($array, callable $callback = null, $default = null)
    {
        if ($callback === null) {
            if (empty($array)) {
                return $default;
            }


            return current($array);
        }

        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return $default;
    }

    /**
     * Return items where conditions are met.
     *
     * @param array $array
     * @param array|callable $where if callable expect value, key params to be passed
     *
     * @return array
     */
    public static function where($array, $where): array
    {
        if (is_callable($where)) {
            return array_filter($array, $where, ARRAY_FILTER_USE_BOTH);
        }

        return array_filter(
            $array,
            static function ($item) use ($where) {
                foreach ($where as $key => $value) {
                    if (!static::exists($item, $key) || $item[$key] !== $value) {
                        return false;
                    }
                }
                return true;
            }
        );
    }

    /**
     * Check if the value is array or array alike.
     * @param $value
     * @return bool
     */
    public static function isArray($value)
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }
}