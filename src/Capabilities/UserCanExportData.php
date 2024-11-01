<?php

namespace TotalRating\Capabilities;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\WordPress\Capability;

/**
 * Class ExportData
 *
 * @package TotalRating\Capabilities
 */
class UserCanExportData extends Capability
{
    const NAME = 'totalrating_export_data';
}