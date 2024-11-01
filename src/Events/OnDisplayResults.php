<?php

namespace TotalRating\Events;
! defined( 'ABSPATH' ) && exit();



use TotalRatingVendors\TotalSuite\Foundation\Event;

class OnDisplayResults extends Event
{
    const ALIAS = 'totalrating/display/results';

    /**
     * @var string
     */
    public $widgetUid;

    /**
     * constructor.
     *
     * @param string $widgetUid
     */
    public function __construct(string $widgetUid)
    {
        $this->widgetUid = $widgetUid;
    }
}