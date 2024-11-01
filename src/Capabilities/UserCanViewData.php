<?php

namespace TotalRating\Capabilities;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\WordPress\Capability;

/**
 * Class ViewData
 *
 * @package TotalRating\Capabilities
 */
class UserCanViewData extends Capability
{
    const NAME = 'totalrating_view_data';
}