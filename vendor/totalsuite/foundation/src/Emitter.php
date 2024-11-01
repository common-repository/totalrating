<?php

namespace TotalRatingVendors\TotalSuite\Foundation;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\League\Event\Emitter as AbstractEmitter;
use TotalRatingVendors\TotalSuite\Foundation\Helpers\Concerns\ResolveFromContainer;

/**
 * Class Emitter
 *
 * @package TotalSuite\Foundation
 */
class Emitter extends AbstractEmitter
{
    use ResolveFromContainer;
}