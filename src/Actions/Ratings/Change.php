<?php

namespace TotalRating\Actions\Ratings;
! defined( 'ABSPATH' ) && exit();



use TotalRating\Actions\Concerns\ChecksReCaptcha;
use TotalRating\Models\Rating;
use TotalRating\Models\Widget;
use TotalRating\Tasks\Rating\ApplyLimitations;
use TotalRating\Tasks\Rating\CanChange;
use TotalRating\Tasks\Rating\ChangeRating;
use TotalRating\Tasks\Utils\GetIP;
use TotalRating\Tasks\Utils\GetUserAgent;
use TotalRatingVendors\TotalSuite\Foundation\Action;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Http\Response;

class Change extends Action
{
    use ChecksReCaptcha;

    /**
     * @return Response
     * @throws Exception
     */
    public function execute(): Response
    {
        $data                = $this->request->getParsedBody();
        $data['entity_meta'] = $this->request->getParsedBodyParam('entity_meta', []);
        $data['ip']          = GetIP::invoke();
        $data['agent']       = GetUserAgent::invoke();

        $rating = Rating::byUid($this->request->getParam('rating_uid'));
        $widget = Widget::byUidAndActive($rating->widget_uid);

        $canChange = CanChange::invoke($widget, $rating);
        $rating    = ChangeRating::invoke($widget, $rating, $data);

        return ApplyLimitations::invoke($widget, $rating, true);
    }

    /**
     * @inheritDoc
     */
    public function authorize(): bool
    {
        return $this->checkRecaptcha();
    }
}
