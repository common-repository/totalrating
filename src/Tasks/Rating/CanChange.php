<?php

namespace TotalRating\Tasks\Rating;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Exceptions\ChangeRatingUnauthorized;
use TotalRating\Models\Rating;
use TotalRating\Models\Widget;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Task;

/**
 * Class CanChange
 *
 * @package TotalRating\Tasks\Rating
 * @method static invoke(Widget $widget, Rating $rating = null)
 * @method static invokeWithFallback($fallback, Widget $widget, Rating $rating = null)
 */
class CanChange extends Task
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
     * constructor.
     *
     * @param $widget
     * @param $rating
     */
    public function __construct(Widget $widget, Rating $rating = null)
    {
        $this->widget = $widget;
        $this->rating = $rating;
    }

    /**
     * @inheritDoc
     */
    protected function validate()
    {
        return true;
    }

    /**
     * @return bool|mixed
     * @throws Exception
     */
    protected function execute()
    {
        ChangeRatingUnauthorized::throwUnless($this->widget->isChangeAllowed(), 'Changing operations are not allowed.');
        ChangeRatingUnauthorized::throwIf(
            $this->rating && !$this->rating->isAccepted(),
            'You cannot change this rating.'
        );
        ChangeRatingUnauthorized::throwIf(
            !$this->rating && $this->widget->isLimitationByUserEnabled() && !is_user_logged_in()
        );

        return true;
    }
}
