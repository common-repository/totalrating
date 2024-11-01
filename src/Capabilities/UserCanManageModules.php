<?php

namespace TotalRating\Capabilities;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\WordPress\Capability;

/**
 * Class ManageModules
 *
 * @package TotalRating\Capabilities
 */
class UserCanManageModules extends Capability
{
    const NAME = 'totalrating_manage_modules';
}