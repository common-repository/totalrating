<?php

namespace TotalRating\Events;
! defined( 'ABSPATH' ) && exit();



use TotalRating\Models\Rating;
use TotalRating\Models\Widget;
use TotalRatingVendors\TotalSuite\Foundation\Event;

class OnRatingReceived extends Event
{
    const ALIAS = 'totalrating/rating/received';

    /**
     * @var Widget
     */
    public $widget;

    /**
     * @var Rating
     */
    public $rating;

    /**
     * constructor.
     *
     * @param Widget $widget
     * @param Rating $rating
     */
    public function __construct($widget, $rating)
    {
        $this->widget = $widget;
        $this->rating = $rating;
    }
}