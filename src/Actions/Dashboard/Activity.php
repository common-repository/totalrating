<?php

namespace TotalRating\Actions\Dashboard;
! defined( 'ABSPATH' ) && exit();



use TotalRating\Capabilities\UserCanViewData;
use TotalRating\Tasks\Dashboard\ActivityFeed;
use TotalRatingVendors\TotalSuite\Foundation\Action;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Http\Response;

class Activity extends Action
{
    /**
     * @return Response
     * @throws Exception
     */
    public function execute(): Response
    {
        return ActivityFeed::invoke()->toJsonResponse();
    }

    /**
     * @inheritDoc
     */
    public function authorize(): bool
    {
        return UserCanViewData::check();
    }
}
