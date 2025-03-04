<?php

namespace TotalRatingVendors\Psr\Log;
! defined( 'ABSPATH' ) && exit();


/**
 * Describes a logger-aware instance.
 */
interface LoggerAwareInterface
{
    /**
     * Sets a logger instance on the object.
     *
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(LoggerInterface $logger);
}
