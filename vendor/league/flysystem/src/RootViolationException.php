<?php

namespace TotalRatingVendors\League\Flysystem;
! defined( 'ABSPATH' ) && exit();


use LogicException;

class RootViolationException extends LogicException implements FilesystemException
{
    //
}
