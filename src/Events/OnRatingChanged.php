<?php

namespace TotalRating\Events;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Models\Rating;
use TotalRating\Models\Widget;
use TotalRatingVendors\TotalSuite\Foundation\Event;

class OnRatingChanged extends Event
{
    const ALIAS = 'totalrating/rating/changed';

    /**
     * @var Widget
     */
    public $widget;

    /**
     * @var Rating
     */
    public $rating;

    /**
     * @var Rating
     */
    public $oldRating;

    /**
     * constructor.
     *
     * @param Widget $widget
     * @param Rating $rating
     * @param Rating $oldRating
     */
    public function __construct($widget, $rating, $oldRating)
    {
        $this->widget    = $widget;
        $this->rating    = $rating;
        $this->oldRating = $oldRating;
    }
}