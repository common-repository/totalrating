<?php


namespace TotalRatingVendors\TotalSuite\Foundation\WordPress\Scheduler;
! defined( 'ABSPATH' ) && exit();



use TotalRatingVendors\TotalSuite\Foundation\WordPress\Scheduler;

abstract class CronJob
{
    abstract public function execute();

    /**
     * @return int
     */
    public function getStartTime()
    {
        return time();
    }

    /**
     * @return string
     */
    public function getRecurrence()
    {
        return Scheduler::SCHEDULE_HOURLY;
    }

    /**
     * @return void
     */
    public function __invoke() {
        $this->execute();
    }
}