<?php

namespace TotalRating\Tasks\Rating;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Events\OnRatingChanged;
use TotalRating\Exceptions\InvalidWidgetAttribute;
use TotalRating\Exceptions\InvalidWidgetAttributePoint;
use TotalRating\Models\Attribute;
use TotalRating\Models\Point;
use TotalRating\Models\Rating;
use TotalRating\Models\Widget;
use TotalRating\Plugin;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Task;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Tasks\ValidateInput;

/**
 * Class ChangeRating
 *
 * @package TotalRating\Tasks\Rating
 * @method static Rating invoke(Widget $widget, Rating $rating, array $data, $emitEvent = true)
 * @method static Rating invokeWithFallback($fallback, Widget $widget, Rating $rating, array $data, $emitEvent = true)
 */
class ChangeRating extends Task
{
    /**
     * @var string
     */
    protected $widget;

    /**
     * @var mixed
     */
    protected $rating;
    /**
     * @var null
     */
    protected $data;

    /**
     * @var bool
     */
    protected $emit;

    /**
     * constructor.
     *
     * @param  Widget  $widget
     * @param  Rating  $rating
     * @param  array  $data
     */
    public function __construct(Widget $widget, Rating $rating, array $data, $emitEvent = true)
    {
        $this->widget = $widget;
        $this->rating = $rating;
        $this->data   = $data;
        $this->emit   = $emitEvent;
    }

    /**
     * @inheritDoc
     */
    protected function validate()
    {
        return ValidateInput::invoke(
            [
                'rating_uid' => 'required|alpha_dash|min:36|max:255',
                'point_uid'  => 'required|alpha_dash|min:36|max:255',
                'comment'    => 'string|max:2048',
                'ip'         => 'required|string',
                'agent'      => 'required|string',
            ],
            $this->data
        );
    }

    /**
     * @return Rating
     * @throws Exception
     * @throws \TotalRatingVendors\TotalSuite\Foundation\Exceptions\DatabaseException
     * @noinspection NullPointerExceptionInspection
     */
    protected function execute()
    {
        /**
         * @var Attribute $attribute
         * @var Point $point
         */
        $attribute = $this->widget->getRatingAttribute($this->rating->attribute_uid);
        InvalidWidgetAttribute::throwUnless(
            (bool) $attribute,
            'You cannot change the rating, invalid attribute provided.'
        );

        $point = $attribute->getPoint($this->data['point_uid']);
        InvalidWidgetAttributePoint::throwUnless(
            (bool) $point,
            'You cannot change the rating, invalid point provided.'
        );

        $updateOriginalRating = Plugin::options('advanced.updateOriginalRating.enabled', false);

        if ($updateOriginalRating) {
            $this->rating->updated_at  = date('Y-m-d H:i:s');
            $this->rating->point_uid   = $point->uid;
            $this->rating->value       = $point->value;
            $this->rating->entity_meta = ['original_rating_uid' => $this->rating->uid] + $this->rating->entity_meta;
            $this->rating->comment     = isset($this->data['comment']) ? esc_html($this->data['comment']) : null;
            $this->rating->ip          = esc_html($this->data['ip']);
            $this->rating->agent       = esc_html($this->data['agent']);

            Exception::throwUnless($this->rating->update(), 'Unable to save changes.');

            $this->rating->attribute = $attribute;
            $entity                  = $this->rating->getEntity();

            $this->rating->attribute->withHelpers($entity);

            if (!$this->widget->isResultsHiddenForAll()) {
                $this->rating->attribute->withStatistics($entity);
            }

            $this->rating->point = $point;

            $this->rating->attribute['rating']  = $this->rating->uid;
            $this->rating->attribute['checked'] = $point->uid;

            $newRating = $this->rating;
        } else {
            $this->rating->status     = Rating::STATUS_CHANGED;
            $this->rating->updated_at = date('Y-m-d H:i:s');

            Exception::throwUnless($this->rating->update(), 'Unable to save changes.');

            $newRating = CreateRating::invoke(
                $this->widget,
                [
                    'widget_uid'    => $this->widget->uid,
                    'attribute_uid' => $this->rating->attribute_uid,
                    'point_uid'     => $point->uid,
                    'entity_id'     => $this->rating->entity_id,
                    'entity_type'   => $this->rating->entity_type,
                    'entity_meta'   => ['original_rating_uid' => $this->rating->uid] + $this->rating->entity_meta,
                    'ip'            => $this->data['ip'],
                    'agent'         => $this->data['agent'],
                    'context'       => $this->rating->context,
                    'comment'       => $this->data['comment'] ?? null,
                ],
                false
            );
        }

        if ($this->emit) {
            OnRatingChanged::emit($this->widget, $newRating, $this->rating);
        }

        return $newRating;
    }
}
