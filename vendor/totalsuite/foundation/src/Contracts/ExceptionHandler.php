<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Contracts;
! defined( 'ABSPATH' ) && exit();



use Throwable;

/**
 * Interface ExceptionHandlerInterface
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Contracts
 */
interface ExceptionHandler
{
    /**
     * @param Throwable $e
     *
     * @return mixed
     */
    public function handle(Throwable $e);
}