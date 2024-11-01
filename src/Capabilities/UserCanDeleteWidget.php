<?php

namespace TotalRating\Capabilities;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\WordPress\Capability;

/**
 * Class DeleteWidget
 *
 * @package TotalRating\Capabilities
 */
class UserCanDeleteWidget extends Capability
{
    const NAME = 'totalrating_delete_widget';
}