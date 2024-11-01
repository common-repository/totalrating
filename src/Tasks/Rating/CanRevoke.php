<?php

namespace TotalRating\Tasks\Rating;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Exceptions\RevokeRatingUnauthorized;
use TotalRating\Models\Rating;
use TotalRating\Models\Widget;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Task;

/**
 * Class CanRevoke
 *
 * @throws Exception
 * @package TotalRating\Tasks\Rating
 * @method static boolean invoke(Widget $widget, Rating $rating = null)
 * @method static boolean invokeWithFallback($fallback, Widget $widget, Rating $rating = null)
 */
class CanRevoke extends Task
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
     * @param  Widget  $widget
     * @param  Rating  $rating
     */
    public function __construct(Widget $widget, Rating $rating)
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
     * @return bool
     * @throws Exception
     */
    protected function execute()
    {
        RevokeRatingUnauthorized::throwUnless($this->widget->isRevokeAllowed(), 'Revoke operations are not allowed.');
        RevokeRatingUnauthorized::throwIf(
            $this->rating && !$this->rating->isAccepted(),
            'You cannot revoke this rating.'
        );
        RevokeRatingUnauthorized::throwIf(
            !$this->rating && $this->widget->isLimitationByUserEnabled() && !is_user_logged_in()
        );

        return true;
    }
}
