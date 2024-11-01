<?php

namespace TotalRating\Tasks\Widget;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Models\Widget;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\DatabaseException;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Task;

/**
 * Class TrashWidget
 *
 * @package TotalRating\Tasks\Widget
 * @method static Widget invoke(string $widgetUid)
 * @method static Widget invokeWithFallback($fallback, string $widgetUid)
 */
class TrashWidget extends Task
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
     * @return Widget
     * @throws Exception
     * @throws DatabaseException
     */
    public function execute()
    {
        $widget = Widget::byUID($this->widgetUid);

        $widget->status     = Widget::STATUS_DELETED;
        $widget->enabled    = false;
        $widget->deleted_at = date('Y-m-d H:i:s');

        Exception::throwUnless($widget->save(), 'Could not delete the widget');

        return $widget;
    }
}
