<?php


namespace TotalRating\Capabilities;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\WordPress\Capability;

/**
 * Class ManageOption
 *
 * @package TotalRating\Capabilities
 */
class UserCanManageOptions extends Capability
{
    const NAME = 'totalrating_manage_options';
}