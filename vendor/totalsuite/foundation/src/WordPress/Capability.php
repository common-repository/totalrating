<?php

namespace TotalRatingVendors\TotalSuite\Foundation\WordPress;
! defined( 'ABSPATH' ) && exit();


abstract class Capability
{
    const NAME = self::class;

    /**
     * @param mixed ...$arguments
     *
     * @return bool
     */
    public static function check(...$arguments): bool
    {
        return current_user_can(static::NAME, ...$arguments);
    }
}