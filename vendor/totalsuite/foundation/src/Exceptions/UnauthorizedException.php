<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Exceptions;
! defined( 'ABSPATH' ) && exit();



/**
 * Class UnauthorizedException
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Exceptions
 */
class UnauthorizedException extends Exception
{
    const CODE = 401;
}