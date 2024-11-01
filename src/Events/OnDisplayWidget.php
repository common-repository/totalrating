<?php

namespace TotalRating\Events;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Event;

class OnDisplayWidget extends Event
{
    const ALIAS = 'totalrating/display/widget';

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