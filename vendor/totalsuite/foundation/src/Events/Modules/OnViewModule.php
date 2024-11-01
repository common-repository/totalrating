<?php


namespace TotalRatingVendors\TotalSuite\Foundation\Events\Modules;
! defined( 'ABSPATH' ) && exit();



use TotalRatingVendors\TotalSuite\Foundation\Event;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Modules\Definition;

class OnViewModule extends Event {
    /**
     * @var Definition
     */
    public $definition;

    /**
     * OnActivateModule constructor.
     *
     * @param  Definition  $definition
     */
    public function __construct(Definition $definition)
    {
        $this->definition = $definition;
    }

}