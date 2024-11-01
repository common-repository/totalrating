<?php

namespace TotalRating\Capabilities;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\WordPress\Capability;

/**
 * Class CreateWidget
 *
 * @package TotalRating\Capabilities
 */
class UserCanCreateWidget extends Capability
{
    const NAME = 'totalrating_create_widget';
}