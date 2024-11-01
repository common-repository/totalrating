<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Filesystem;
! defined( 'ABSPATH' ) && exit();



use TotalRatingVendors\League\Flysystem\Adapter\CanOverwriteFiles;
use TotalRatingVendors\League\Flysystem\Adapter\Local;

/**
 * Class LocalAdapter
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Filesystem
 */
class LocalAdapter extends Local implements CanOverwriteFiles
{

}