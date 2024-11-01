<?php

namespace TotalRating\Tasks\Dashboard;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Models\Rating;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\DatabaseException;
use TotalRatingVendors\TotalSuite\Foundation\Support\Collection;
use TotalRatingVendors\TotalSuite\Foundation\Task;

/**
 * Class ActivityFeed
 *
 * @package TotalRating\Tasks\Dashboard
 * @method static Collection invoke(int $count = 5)
 * @method static Collection invokeWithFallback($fallback = false, int $count = 5)
 */
class ActivityFeed extends Task
{
    /**
     * @inheritDoc
     */
    protected function validate()
    {
        return true;
    }

    /**
     * @param int $count
     *
     * @return Collection
     * @throws DatabaseException
     */
    protected function execute($count = 5)
    {
        return Rating::query()->orderBy('created_at', 'desc')->limit($count)->get();
    }
}
