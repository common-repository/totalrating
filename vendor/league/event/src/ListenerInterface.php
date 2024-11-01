<?php

namespace TotalRatingVendors\League\Event;
! defined( 'ABSPATH' ) && exit();


interface ListenerInterface
{
    /**
     * Handle an event.
     *
     * @param EventInterface $event
     *
     * @return void
     */
    public function handle(EventInterface $event);

    /**
     * Check whether the listener is the given parameter.
     *
     * @param mixed $listener
     *
     * @return bool
     */
    public function isListener($listener);
}
