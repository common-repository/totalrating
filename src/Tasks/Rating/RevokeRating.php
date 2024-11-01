<?php

namespace TotalRating\Tasks\Rating;
! defined( 'ABSPATH' ) && exit();



use TotalRating\Events\OnRatingRevoked;
use TotalRating\Models\Rating;
use TotalRating\Models\Widget;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Task;

/**
 * Class RevokeRating
 *
 * @package TotalRating\Tasks\Rating
 * @method static Rating invoke(Widget $rating, Rating $rating, $emitEvent = true)
 * @method static Rating invokeWithFallback($fallback, Widget $rating, Rating $rating, $emitEvent = true)
 */
class RevokeRating extends Task
{
    /**
     * @var Widget
     */
    protected $widget;

    /**
     * @var Rating
     */
    protected $rating;

    /**
     * @var bool
     */
    protected $emit;

    /**
     * constructor.
     *
     * @param  Widget  $widget
     * @param  Rating  $rating
     * @param  bool  $emitEvent
     */
    public function __construct(Widget $widget, Rating $rating, $emitEvent = true)
    {
        $this->widget = $widget;
        $this->rating = $rating;
        $this->emit   = $emitEvent;
    }

    /**
     * @inheritDoc
     */
    protected function validate()
    {
        return true;
    }

    /**
     * @return Rating
     * @throws Exception
     */
    protected function execute(): Rating
    {
        $this->rating->status     = Rating::STATUS_REVOKED;
        $this->rating->deleted_at = date('Y-m-d H:i:s');

        Exception::throwUnless($this->rating->update(), 'Revoke not available, try again.');

        if (!$this->widget->isResultsHiddenForAll()) {
            $entity                  = $this->rating->getEntity();
            $this->rating->attribute = $this->widget->getRatingAttribute($this->rating->attribute_uid)
                                                    ->withStatistics($entity)
                                                    ->fromRevokeContext();
        }

        // @TODO Decrement the cookies values

        if ($this->emit) {
            OnRatingRevoked::emit($this->widget, $this->rating);
        }

        return $this->rating;
    }
}
