<?php

namespace TotalRatingVendors\TotalSuite\Foundation\WordPress;
! defined( 'ABSPATH' ) && exit();


/**
 * Class Roles
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\WordPress
 */
class Roles
{
    const ADMINISTRATOR = 'administrator';
    const EDITOR = 'editor';
    const AUTHOR = 'author';
    const CONTRIBUTOR = 'contributor';
    const SUBSCRIBER = 'subscriber';

    /**
     * @param string $role
     * @param array $capabilities
     */
    public static function set($role, array $capabilities)
    {
        $role = get_role($role);

        if ($role === null) {
            return;
        }

        foreach ($capabilities as $capability) {
            if (!$role->has_cap($capability)) {
                $role->add_cap($capability, true);
            }
        }
    }

    /**
     * @param string $role
     * @param        $capabilities
     */
    public static function remove($role, array $capabilities)
    {
        $role = get_role($role);

        if ($role === null) {
            return;
        }

        foreach ($capabilities as $capability) {
            if ($role->has_cap($capability)) {
                $role->remove_cap($capability);
            }
        }
    }
}