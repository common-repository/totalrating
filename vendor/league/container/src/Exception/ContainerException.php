<?php

namespace TotalRatingVendors\League\Container\Exception;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\Psr\Container\ContainerExceptionInterface;
use RuntimeException;

class ContainerException extends RuntimeException implements ContainerExceptionInterface
{
}
