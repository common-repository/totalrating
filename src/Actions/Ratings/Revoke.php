<?php

namespace TotalRating\Actions\Ratings;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Actions\Concerns\ChecksReCaptcha;
use TotalRating\Models\Rating;
use TotalRating\Models\Widget;
use TotalRating\Tasks\Rating\CanRevoke;
use TotalRating\Tasks\Rating\PurgeLimitations;
use TotalRating\Tasks\Rating\RevokeRating;
use TotalRating\Tasks\Utils\GetIP;
use TotalRating\Tasks\Utils\GetUserAgent;
use TotalRatingVendors\TotalSuite\Foundation\Action;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Http\Response;

class Revoke extends Action
{
    use ChecksReCaptcha;

    /**
     * @return Response
     * @throws Exception
     */
    public function execute(): Response
    {
        $data          = $this->request->getParsedBody();
        $data['ip']    = GetIP::invoke();
        $data['agent'] = GetUserAgent::invoke();

        $rating = Rating::byUid($this->request->getParam('rating_uid'));
        $widget = Widget::byUidAndActive($rating->widget_uid);

        $canRevoke = CanRevoke::invoke($widget, $rating);
        $rating    = RevokeRating::invoke($widget, $rating);

        return PurgeLimitations::invoke($widget, $rating);
    }

    /**
     * @inheritDoc
     */
    public function authorize(): bool
    {
        return $this->checkRecaptcha();
    }
}
