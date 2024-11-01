<?php

namespace TotalRating\Actions\Ratings;
! defined( 'ABSPATH' ) && exit();



use TotalRating\Actions\Concerns\ChecksReCaptcha;
use TotalRating\Models\Widget;
use TotalRating\Tasks\Rating\ApplyLimitations;
use TotalRating\Tasks\Rating\CanRate;
use TotalRating\Tasks\Rating\CreateRating;
use TotalRating\Tasks\Rating\ResolveEntity;
use TotalRating\Tasks\Utils\GetIP;
use TotalRating\Tasks\Utils\GetUserAgent;
use TotalRatingVendors\TotalSuite\Foundation\Action;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Http\Response;

class Create extends Action
{
    use ChecksReCaptcha;

    /**
     * @return Response
     * @throws Exception
     */
    public function execute()
    {
        $data                = $this->request->getParsedBody();
        $data['entity_meta'] = $this->request->getParsedBodyParam('entity_meta', []);
        $data['ip']          = GetIP::invoke();
        $data['agent']       = GetUserAgent::invoke();

        $entity = ResolveEntity::invoke(
            $data['entity_id'] ?? null,
            $data['entity_type'] ?? null,
            (array) ($data['entity_meta'] ?? [])
        );

        $widget = Widget::byUidAndActive($this->request->getParsedBodyParam('widget_uid'));

        CanRate::invoke($widget, $entity, $data);
        $rating = CreateRating::invoke($widget, $data);

        return ApplyLimitations::invoke($widget, $rating);
    }

    /**
     * @inheritDoc
     */
    public function authorize(): bool
    {
        return $this->checkRecaptcha();
    }
}
