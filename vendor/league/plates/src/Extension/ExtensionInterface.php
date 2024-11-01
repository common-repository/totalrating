<?php

namespace TotalRatingVendors\League\Plates\Extension;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\League\Plates\Engine;

/**
 * A common interface for extensions.
 */
interface ExtensionInterface
{
    public function register(Engine $engine);
}
