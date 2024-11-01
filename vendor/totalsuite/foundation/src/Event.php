<?php

namespace TotalRatingVendors\TotalSuite\Foundation;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\League\Event\AbstractEvent;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Plugin;

/**
 * Class Event
 *
 * @package TotalSuite\Foundation
 */
class Event extends AbstractEvent
{
    const ALIAS = null;

    /**
     * Shorthand to listen to an event.
     *
     * @param callable|object $listener
     * @param int $priority
     * @return Emitter
     */
    public static function listen($listener, $priority = Emitter::P_NORMAL): Emitter
    {
        return Plugin::listen(static::class, $listener, $priority);
    }

    /**
     * Shorthand to emit an event.
     *
     * @param mixed ...$arguments
     * @return Event
     */
    public static function emit(...$arguments): Event
    {
        return Plugin::emit(new static(...$arguments));
    }

    /**
     * Alias. Used for WP do_action.
     *
     * @return string|null
     */
    public static function alias()
    {
        return static::ALIAS ?? static::class;
    }
}