<?php

namespace TotalRatingVendors\League\Flysystem;
! defined( 'ABSPATH' ) && exit();


use RuntimeException;

class ConnectionRuntimeException extends RuntimeException implements FilesystemException
{
}
