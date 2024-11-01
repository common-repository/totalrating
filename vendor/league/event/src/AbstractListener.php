<?php

namespace TotalRatingVendors\League\Event;
! defined( 'ABSPATH' ) && exit();


abstract class AbstractListener implements ListenerInterface
{
    /**
     * @inheritdoc
     */
    public function isListener($listener)
    {
        return $this === $listener;
    }
}
