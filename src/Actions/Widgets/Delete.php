<?php

namespace TotalRating\Actions\Widgets;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Capabilities\UserCanDeleteWidget;
use TotalRating\Tasks\Widget\DeleteWidget;
use TotalRatingVendors\TotalSuite\Foundation\Action;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Http\Response;

class Delete extends Action
{
    /**
     * @param $widgetUid
     *
     * @return Response
     * @throws Exception
     */
    public function execute(string $widgetUid): Response
    {
        return DeleteWidget::invoke($widgetUid)
                           ->toJsonResponse();
    }

    /**
     * @inheritDoc
     */
    public function authorize(): bool
    {
        return UserCanDeleteWidget::check();
    }

    public function getParams(): array
    {
        return [
            'widgetUid' => [
                'expression'        => '(?<widgetUid>([\w-]+))',
                'sanitize_callback' => function ($widgetUid) {
                    return (string)$widgetUid;
                },
            ],
        ];
    }
}
