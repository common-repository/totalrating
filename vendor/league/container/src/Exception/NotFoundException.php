<?php

namespace TotalRatingVendors\League\Container\Exception;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\Psr\Container\NotFoundExceptionInterface;
use InvalidArgumentException;

class NotFoundException extends InvalidArgumentException implements NotFoundExceptionInterface
{
}
