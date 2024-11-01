<?php

namespace TotalRating\Actions\Widgets;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Capabilities\UserCanDeleteWidget;
use TotalRating\Tasks\Widget\RestoreWidget;
use TotalRatingVendors\TotalSuite\Foundation\Action;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Http\Response;

class Restore extends Action
{
    /**
     * @param $widgetUid
     *
     * @return Response
     * @throws Exception
     */
    public function execute($widgetUid): Response
    {
        return RestoreWidget::invoke($widgetUid)
                            ->toJsonResponse();
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
                'sanitize_callback' => static function ($widgetUid) {
                    return (string)$widgetUid;
                },
            ],
        ];
    }
}
