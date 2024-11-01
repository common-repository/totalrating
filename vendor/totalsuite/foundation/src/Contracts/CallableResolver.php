<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Contracts;
! defined( 'ABSPATH' ) && exit();



interface CallableResolver
{
    /**
     * @param $class
     *
     * @return callable
     */
    public function resolve($class): callable;
}