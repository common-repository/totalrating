<?php

namespace TotalRating\Actions\Widgets;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Models\Widget;
use TotalRating\Tasks\Rating\ResolveEntity;
use TotalRatingVendors\TotalSuite\Foundation\Action;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Http\Response;

class GetPublic extends Action
{
    /**
     * @param string $widgetUid
     *
     * @return Response
     * @throws Exception
     */
    public function execute($widgetUid): Response
    {
        $data                = $this->request->getParams();
        $data['entity_meta'] = $this->request->getParam('entity_meta', []);

        $entity = ResolveEntity::invokeWithFallback(null,
            $data['entity_id'] ?? null,
            $data['entity_type'] ?? null,
            (array)($data['entity_meta'] ?? [])
        );

        return Widget::byUidAndActive($widgetUid)
                     ->toPublic($entity)
                     ->toJsonResponse();
    }

    /**
     * @inheritDoc
     */
    public function authorize(): bool
    {
        return true;
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
