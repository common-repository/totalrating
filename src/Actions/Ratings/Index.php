<?php

namespace TotalRating\Actions\Ratings;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Capabilities\UserCanViewWidgets;
use TotalRating\Models\Rating;
use TotalRating\Tasks\Rating\GetRatings;
use TotalRatingVendors\TotalSuite\Foundation\Action;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Http\Response;

class Index extends Action
{
    /**
     * @return Response
     * @throws Exception
     */
    public function execute(): Response
    {
        $filters = $this->request->getQueryParams();

        return GetRatings::invoke($filters, true)
                         ->map(
                             static function (Rating $rating) {
                                 return $rating->withEntity()
                                               ->withUser();
                             }
                         )
                         ->toJsonResponse();
    }

    /**
     * @inheritDoc
     */
    public function authorize(): bool
    {
        return UserCanViewWidgets::check();
    }
}
