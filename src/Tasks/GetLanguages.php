<?php

namespace TotalRating\Tasks;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Task;

/**
 * Class GetLanguages
 *
 * @package TotalRating\Tasks\Options
 * @method static array invoke()
 * @method static array invokeWithFallback(array $fallback)
 */
class GetLanguages extends Task
{
    /**
     * @inheritDoc
     */
    protected function validate()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function execute()
    {
        return [];
    }
}
