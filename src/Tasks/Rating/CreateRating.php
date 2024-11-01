<?php

namespace TotalRating\Tasks\Rating;
! defined( 'ABSPATH' ) && exit();



use TotalRating\Events\OnRatingReceived;
use TotalRating\Filters\FilterCreatedRating;
use TotalRating\Models\Rating;
use TotalRating\Models\Widget;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\NotFoundException;
use TotalRatingVendors\TotalSuite\Foundation\Support\Arrays;
use TotalRatingVendors\TotalSuite\Foundation\Support\Strings;
use TotalRatingVendors\TotalSuite\Foundation\Task;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Tasks\ValidateInput;

/**
 * Class CreateRating
 *
 * @package TotalRating\Tasks\Rating
 * @method static Rating invoke(Widget $widget, array $data, $emitEvent = true)
 * @method static Rating invokeWithFallback($fallback, Widget $widget, array $data, $emitEvent = true)
 */
class CreateRating extends Task
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var Widget
     */
    protected $widget;

    /**
     * @var bool
     */
    protected $emit;

    /**
     * constructor.
     *
     * @param  Widget  $widget
     * @param  array  $data
     * @param  bool  $emitEvent
     */
    public function __construct(Widget $widget, array $data, $emitEvent = true)
    {
        $this->widget = $widget;
        $this->data   = Arrays::only(
            $data,
            [
                'widget_uid',
                'attribute_uid',
                'point_uid',
                'entity_id',
                'entity_type',
                'entity_meta',
                'comment',
                'ip',
                'agent',
                'context',
            ]
        );
        $this->emit   = $emitEvent;
    }


    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function validate()
    {
        return ValidateInput::invoke(
            [
                'widget_uid'    => 'required|min:36|max:255',
                'attribute_uid' => 'required|min:36|max:255',
                'point_uid'     => 'required|min:5|max:255',
                'entity_id'     => 'required|string',
                'entity_type'   => 'required',
                'entity_meta'   => 'array',
                'comment'       => 'string|max:4048',
                'context'       => 'string',
                'ip'            => 'required|string',
                'agent'         => 'required|string',
            ],
            $this->data
        );
    }

    /**
     * @return Rating
     * @throws Exception
     * @throws \Exception
     */
    protected function execute()
    {
        $attribute = $this->widget->getRatingAttribute($this->data['attribute_uid']);

        NotFoundException::throwUnless($attribute, 'Invalid attribute');

        $point = $attribute->getPoint($this->data['point_uid']);

        NotFoundException::throwUnless($point, 'Invalid attribute');

        $value = $point->value;

        $rating = new Rating();
        $rating->hydrate($this->data);

        $rating->user_id    = get_current_user_id();
        $rating->status     = Rating::STATUS_ACCEPTED;
        $rating->uid        = Strings::uid();
        $rating->value      = $value;
        $rating->created_at = date('Y-m-d H:i:s');
        $rating->comment    = isset($this->data['comment']) ? esc_html($this->data['comment']) : null;
        $rating->ip         = esc_html($this->data['ip']);
        $rating->agent      = esc_html($this->data['agent']);

        Exception::throwUnless($rating->save(), 'Could not save the rating');

        $rating->attribute = $attribute;
        $entity            = $rating->getEntity();

        $rating->attribute->withHelpers($entity);

        $rating->point = $point;

        $rating->attribute['rating']  = $rating->uid;
        $rating->attribute['checked'] = $point->uid;

        if (!$this->widget->isResultsHiddenForAll()) {
            $rating->attribute->withStatistics($entity);
        }

        if ($this->emit) {
            OnRatingReceived::emit($this->widget, $rating);
        }

        return FilterCreatedRating::apply($rating);
    }
}
