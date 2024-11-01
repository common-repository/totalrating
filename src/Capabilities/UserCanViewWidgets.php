<?php

namespace TotalRating\Capabilities;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\WordPress\Capability;

/**
 * Class ViewWidgets
 *
 * @package TotalRating\Capabilities
 */
class UserCanViewWidgets extends Capability
{
    const NAME = 'totalrating_view_widgets';
}