<?php

namespace TotalRating\Actions\Ratings;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Capabilities\UserCanDeleteWidget;
use TotalRating\Tasks\Rating\DeleteRating;
use TotalRatingVendors\TotalSuite\Foundation\Action;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Http\Response;

class Delete extends Action
{
    /**
     * @param string $ratingUid
     * @return Response
     * @throws Exception
     */
    public function execute($ratingUid): Response
    {
        return DeleteRating::invoke($ratingUid)->toJsonResponse();
    }

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

    /**
     * @inheritDoc
     */
    public function authorize(): bool
    {
        return UserCanDeleteWidget::check();
    }
}
