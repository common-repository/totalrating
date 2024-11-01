<?php

namespace TotalRating\Tasks\Dashboard;
! defined( 'ABSPATH' ) && exit();



use TotalRating\Models\Rating;
use TotalRatingVendors\TotalSuite\Foundation\Database\Query\Expression;
use TotalRatingVendors\TotalSuite\Foundation\Database\Query\Func;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\DatabaseException;
use TotalRatingVendors\TotalSuite\Foundation\Support\Collection;
use TotalRatingVendors\TotalSuite\Foundation\Task;

/**
 * Class GetInsights
 *
 * @package TotalRating\Tasks\Dashboard
 * @method static Collection invoke()
 * @method static Collection invokeWithFallback($fallback = false)
 */
class GetInsights extends Task
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
        $total = (int)Rating::query()->count();

        $today = (int)Rating::query()->whereDate('created_at', date('Y-m-d'))->count();

        /**
         * @var Collection $months
         */
        $months = Rating::query()->column(new Expression('COUNT(*)'), 'total')
                                 ->column(
                                     new Func('DATE_FORMAT', 'created_at', new Expression('"%Y-%m"')),
                                     'month'
                                 )
                                 ->groupBy('month')
                                 ->orderBy('month')
                                 ->all();

        return Collection::create(compact('total', 'today', 'months'));
    }
}
