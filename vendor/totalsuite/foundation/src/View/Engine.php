<?php

namespace TotalRatingVendors\TotalSuite\Foundation\View;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Helpers\Concerns\ResolveFromContainer;

/**
 * Class Engine
 *
 * @package TotalSuite\Foundation
 */
class Engine extends \TotalRatingVendors\League\Plates\Engine
{
    use ResolveFromContainer;
}
