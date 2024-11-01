<?php

namespace TotalRating\Tasks\Widget;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Models\Rating;
use TotalRating\Models\Widget;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\DatabaseException;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Task;

/**
 * Class ResetWidget
 *
 * @package TotalRating\Tasks\Widget
 * @method static Widget invoke(string $widgetUid)
 */
class ResetWidget extends Task
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
    public function __construct(string $widgetUid) {
        $this->widgetUid = $widgetUid;
    }

    /**
     * @return bool|mixed|void
     */
    public function validate() {
        return true;
    }

    /**
     * @return Widget
     * @throws Exception
     * @throws DatabaseException
     */
    public function execute(): Widget {
        $widget = Widget::byUID($this->widgetUid);

        Rating::query()
              ->delete()
              ->where('widget_uid', $widget->uid)
              ->execute();


        return $widget;
    }
}
