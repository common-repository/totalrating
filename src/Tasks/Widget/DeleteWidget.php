<?php

namespace TotalRating\Tasks\Widget;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Models\Rating;
use TotalRating\Models\Widget;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\DatabaseException;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Task;

/**
 * Class DeleteWidget
 *
 * @package TotalRating\Tasks\Widget
 * @method static Widget invoke(string $widgetUid)
 * @method static Widget invokeWithFallback($fallback, string $widgetUid)
 */
class DeleteWidget extends Task
{
    /**
     * @var string
     */
    protected $widgetUid;

    /**
     * constructor.
     *
     * @param $widgetUid
     */
    public function __construct(string $widgetUid)
    {
        $this->widgetUid = $widgetUid;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        return true;
    }

    /**
     * @return mixed|null
     * @throws Exception
     * @throws DatabaseException
     */
    public function execute()
    {
        $widget = Widget::byUID($this->widgetUid);

        Exception::throwUnless($widget->delete(), 'Could not delete the widget');

        Rating::query()
              ->delete()
              ->where('widget_uid', $this->widgetUid)
              ->execute();

        return $widget;
    }
}
