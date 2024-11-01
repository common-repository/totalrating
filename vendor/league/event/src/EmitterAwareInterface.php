<?php

namespace TotalRatingVendors\League\Event;
! defined( 'ABSPATH' ) && exit();


interface EmitterAwareInterface
{
    /**
     * Set the Emitter.
     *
     * @param EmitterInterface $emitter
     *
     * @return $this
     */
    public function setEmitter(EmitterInterface $emitter = null);

    /**
     * Get the Emitter.
     *
     * @return EmitterInterface
     */
    public function getEmitter();
}
