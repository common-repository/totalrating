<?php

namespace TotalRatingVendors\TotalSuite\Foundation\WordPress;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\League\Event\EventInterface;
use TotalRatingVendors\TotalSuite\Foundation\Event;
use TotalRatingVendors\TotalSuite\Foundation\Listener;

/**
 * Class ActionBus
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\WordPress
 */
class ActionBus extends Listener
{
    /**
     * Handle an event.
     *
     * @param EventInterface $event
     *
     * @return void
     */
    public function handle(EventInterface $event)
    {
        if (!$event->isPropagationStopped()) {
            do_action($event instanceof Event ? $event::alias() : $event->getName(), $event);
        }
    }
}