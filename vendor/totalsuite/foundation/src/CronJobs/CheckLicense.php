<?php

namespace TotalRatingVendors\TotalSuite\Foundation\CronJobs;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Scheduler;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Scheduler\CronJob;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Tasks\Activation\CheckLicense as CheckLicenseTask;

/**
 * Class CheckLicense
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\CronJobs
 */
class CheckLicense extends CronJob
{
    public function execute()
    {
        try {
            CheckLicenseTask::invoke();
        } catch (Exception $e) {
            return;
        }
    }

    public function getRecurrence()
    {
        return Scheduler::SCHEDULE_DAILY;
    }
}