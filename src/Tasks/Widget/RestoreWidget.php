<?php

namespace TotalRating\Tasks\Widget;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Models\Widget;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\DatabaseException;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Task;

/**
 * Class RestoreWidget
 *
 * @package TotalRating\Tasks\Widget
 * @method static Widget invoke(string $widgetUid)
 * @method static Widget invokeWithFallback($fallback, string $widgetUid)
 */
class RestoreWidget extends Task
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
     * @return bool|mixed|void
     */
    public function validate()
    {
        return true;
    }

    /**
     * @return Widget
     * @throws DatabaseException
     * @throws Exception
     */
    public function execute(): Widget
    {
        $widget = Widget::byUID($this->widgetUid);

        $widget->deleted_at = null;
        $widget->status     = Widget::STATUS_OPEN;
        $widget->enabled    = true;

        Exception::throwUnless($widget->save(), 'Could not restore the widget');

        return $widget;
    }
}
