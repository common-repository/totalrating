<?php

namespace TotalRatingVendors\League\Event;
! defined( 'ABSPATH' ) && exit();


interface GeneratorInterface
{
    /**
     * Release all the added events.
     *
     * @return EventInterface[]
     */
    public function releaseEvents();
}
