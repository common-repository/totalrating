<?php

namespace TotalRating\Actions\Widgets;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Capabilities\UserCanDeleteWidget;
use TotalRating\Tasks\Widget\TrashWidget;
use TotalRatingVendors\TotalSuite\Foundation\Action;
use TotalRatingVendors\TotalSuite\Foundation\Http\Response;

class Trash extends Action
{
    /**
     * @param string $widgetUid
     *
     * @return Response
     */
    public function execute($widgetUid): Response
    {
        return TrashWidget::invoke($widgetUid)->toJsonResponse();
    }

    /**
     * @inheritDoc
     */
    public function authorize(): bool
    {
        return UserCanDeleteWidget::check();
    }

    /**
     * @return array
     */
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
