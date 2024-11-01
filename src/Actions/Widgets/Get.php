<?php

namespace TotalRating\Actions\Widgets;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Capabilities\UserCanViewWidgets;
use TotalRating\Tasks\Widget\GetWidget;
use TotalRatingVendors\TotalSuite\Foundation\Action;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Http\Response;

class Get extends Action
{
    /**
     * @param string $widgetUid
     *
     * @return Response
     * @throws Exception
     */
    public function execute($widgetUid): Response
    {
        return GetWidget::invoke($widgetUid)->toJsonResponse();
    }

    /**
     * @inheritDoc
     */
    public function authorize(): bool
    {
        return UserCanViewWidgets::check();
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
