<?php

namespace TotalRatingVendors\TotalSuite\Foundation\CronJobs;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Contracts\TrackingStorage;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Plugin;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Scheduler;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Scheduler\CronJob;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Tasks\Tracking\SendTrackingRequest;

class TrackEvents extends CronJob
{
    /**
     * @throws Exception|Exception
     */
    public function execute()
    {
        $url = Plugin::env('url.tracking.events');
        $options = Plugin::get(TrackingStorage::class);

        SendTrackingRequest::invoke($url, $options->all());
    }

    public function getRecurrence()
    {
        return Scheduler::SCHEDULE_DAILY;
    }

    public function getStartTime()
    {
        return time();
    }
}