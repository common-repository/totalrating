<?php

namespace TotalRating\Capabilities;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\WordPress\Capability;

/**
 * Class UpdateWidget
 *
 * @package TotalRating\Capabilities
 */
class UserCanUpdateWidget extends Capability
{
    const NAME = 'totalrating_update_widget';
}