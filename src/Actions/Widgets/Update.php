<?php

namespace TotalRating\Actions\Widgets;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Capabilities\UserCanUpdateWidget;
use TotalRating\Tasks\Widget\UpdateWidget;
use TotalRatingVendors\TotalSuite\Foundation\Action;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Http\Response;

class Update extends Action
{
    /**
     * @param $widgetUid
     *
     * @return Response
     * @throws Exception
     */
    public function execute($widgetUid): Response
    {
        return UpdateWidget::invoke($widgetUid, $this->request->getParsedBody())->toJsonResponse();
    }

    /**
     * @inheritDoc
     */
    public function authorize(): bool
    {
        return UserCanUpdateWidget::check();
    }

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
