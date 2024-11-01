<?php

namespace TotalRating\Actions\Ratings;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Capabilities\UserCanViewWidgets;
use TotalRating\Models\Rating;
use TotalRatingVendors\TotalSuite\Foundation\Action;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Http\Response;

class Get extends Action
{
    /**
     * @param string $ratingUid
     *
     * @return Response
     * @throws Exception
     */
    public function execute($ratingUid): Response
    {
        return Rating::byUid($ratingUid)
                     ->withUser()
                     ->withEntity()
                     ->toJsonResponse();
    }

    /**
     * @inheritDoc
     */
    public function authorize(): bool
    {
        return UserCanViewWidgets::check();
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return [
            'ratingUid' => [
                'expression'        => '(?<ratingUid>([\w-]+))',
                'sanitize_callback' => function ($ratingUid) {
                    return (string)$ratingUid;
                },
            ],
        ];
    }
}
