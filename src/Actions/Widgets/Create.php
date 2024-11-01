<?php

namespace TotalRating\Actions\Widgets;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Capabilities\UserCanCreateWidget;
use TotalRating\Tasks\Widget\CreateWidget;
use TotalRatingVendors\TotalSuite\Foundation\Action;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Http\Response;

class Create extends Action
{
    /**
     * @return Response
     * @throws Exception
     */
    public function execute()
    {
        return CreateWidget::invoke($this->request->getParsedBody())
                           ->toJsonResponse();
    }

    /**
     * @inheritDoc
     */
    public function authorize(): bool
    {
        return UserCanCreateWidget::check();
    }
}
