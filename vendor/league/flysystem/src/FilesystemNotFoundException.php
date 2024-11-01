<?php

namespace TotalRatingVendors\League\Flysystem;
! defined( 'ABSPATH' ) && exit();


use LogicException;

/**
 * Thrown when the MountManager cannot find a filesystem.
 */
class FilesystemNotFoundException extends LogicException implements FilesystemException
{
}
