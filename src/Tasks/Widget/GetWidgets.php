<?php

namespace TotalRating\Tasks\Widget;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Models\Widget;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\DatabaseException;
use TotalRatingVendors\TotalSuite\Foundation\Support\Collection;
use TotalRatingVendors\TotalSuite\Foundation\Task;

/**
 * Class GetWidgets
 *
 * @package TotalRating\Tasks\Widget
 * @method static Collection invoke()
 * @method static Collection invokeWithFallback($fallback = false)
 */
class GetWidgets extends Task
{
    /**
     * @inheritDoc
     */
    protected function validate()
    {
        return true;
    }

    /**
     * @return Collection
     * @throws DatabaseException
     */
    protected function execute()
    {
        return Widget::query()
                     ->where('status', '<>', Widget::STATUS_DELETED)
                     ->orderBy('created_at', 'desc')
                     ->get()
                     ->map(
                         static function (Widget $widget) {
                             return $widget->withStatistics();
                         }
                     );
    }
}
