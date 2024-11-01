<?php

namespace TotalRatingVendors\TotalSuite\Foundation\WordPress;
! defined( 'ABSPATH' ) && exit();



use TotalRatingVendors\TotalSuite\Foundation\Helpers\Concerns\ResolveFromContainer;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Scheduler\CronJob;

/**
 * Class Scheduler
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\WordPress
 */
class Scheduler
{
    use ResolveFromContainer;

    const SCHEDULE_EVERY_MINUTE = 'every_minute';
    const SCHEDULE_EVERY_FIVE_MINUTES = 'every_five_minutes';
    const SCHEDULE_HOURLY = 'hourly';
    const SCHEDULE_TWICE_DAILY = 'twicedaily';
    const SCHEDULE_DAILY = 'daily';
    const SCHEDULE_WEEKLY = 'weekly';

    /**
     * @var CronJob[]
     */
    protected $jobs;

    /**
     * JobManager constructor.
     */
    public function __construct()
    {
        add_filter('cron_schedules',
            function ($schedules) {
                $schedules['every_minute'] = array(
                    'interval' => MINUTE_IN_SECONDS,
                    'display' => esc_html__('Every Minute'),
                );

                $schedules['every_five_minutes'] = array(
                    'interval' => 5 * MINUTE_IN_SECONDS,
                    'display' => esc_html__('Every 5 Minutes'),
                );

                return $schedules;
            });
    }


    /**
     * @param $name
     * @param CronJob $cronJob
     */
    public function addCronJob($name, CronJob $cronJob)
    {
        $this->jobs[$name] = $cronJob;
    }

    /**
     * @param $name
     */
    public function removeCronJob($name)
    {
        unset($this->jobs[$name]);
    }

    /**
     * Registre jobs
     */
    public function register()
    {
        foreach ($this->jobs as $name => $cronJob) {
            add_action($name, $cronJob);

            if (!wp_next_scheduled($name)) {
                wp_schedule_event($cronJob->getStartTime(), $cronJob->getRecurrence(), $name);
            }
        }
    }

    /**
     * Un register jobs
     */
    public function unregister()
    {
        foreach ($this->jobs as $name => $cronJob) {
            $timestamp = wp_next_scheduled($name);
            wp_unschedule_event($timestamp, $name);
        }
    }
}