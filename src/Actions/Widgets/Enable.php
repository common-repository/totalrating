<?php

namespace TotalRating\Actions\Widgets;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Capabilities\UserCanUpdateWidget;
use TotalRating\Tasks\Widget\EnableWidget;
use TotalRatingVendors\TotalSuite\Foundation\Action;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Http\Response;

/**
 * Class Enable
 *
 * @package TotalRating\Actions\Widgets
 */
class Enable extends Action
{

    /**
     * @param int $widgetUid
     *
     * @return Response
     * @throws Exception
     */
    public function execute($widgetUid): Response
    {
        $enable = (bool)$this->request->getParam('enabled');

        return EnableWidget::invoke($widgetUid, $enable)
                           ->toJsonResponse();
    }

    /**
     * @inheritDoc
     */
    public function authorize(): bool
    {
        return UserCanUpdateWidget::check();
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
