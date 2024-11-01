<?php

namespace TotalRatingVendors\League\Event;
! defined( 'ABSPATH' ) && exit();


interface ListenerProviderInterface
{
    /**
     * Provide event
     *
     * @param ListenerAcceptorInterface $listenerAcceptor
     *
     * @return $this
     */
    public function provideListeners(ListenerAcceptorInterface $listenerAcceptor);
}
