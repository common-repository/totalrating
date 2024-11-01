<?php

namespace TotalRatingVendors\TotalSuite\Foundation\WordPress;
! defined( 'ABSPATH' ) && exit();


/**
 * Class Filter
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\WordPress
 */
abstract class Filter
{
    /**
     * @param callable $callback
     * @param int $priority
     * @param int $numArgs
     */
    public static function add(callable $callback, $priority = 10, $numArgs = 1)
    {
        add_filter(static::alias(), $callback, $priority, $numArgs);
    }

    protected static function alias()
    {
        return str_replace('\\', '/', strtolower(static::class));
    }

    /**
     * @param mixed ...$arguments
     *
     * @return mixed
     */
    public static function apply(...$arguments)
    {
        return apply_filters(static::alias(), ...$arguments);
    }
}