<?php


namespace TotalRatingVendors\TotalSuite\Foundation\WordPress\Tasks\Activation;
! defined( 'ABSPATH' ) && exit();



use TotalRatingVendors\TotalSuite\Foundation\License;
use TotalRatingVendors\TotalSuite\Foundation\Task;

class RemoveLicense extends Task
{
    protected function validate()
    {
    }

    protected function execute()
    {
        License::instance()->reset();
    }
}