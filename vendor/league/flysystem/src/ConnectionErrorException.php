<?php

namespace TotalRatingVendors\League\Flysystem;
! defined( 'ABSPATH' ) && exit();


use ErrorException;

class ConnectionErrorException extends ErrorException implements FilesystemException
{
}
