<?php


namespace TotalRating\Tasks\Widget;
! defined( 'ABSPATH' ) && exit();



use TotalRating\Models\Widget;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Task;

/**
 * Class EnableWidget
 *
 * @package TotalRating\Tasks\Widget
 * @method static Widget invoke(string $widgetUid, string $status)
 * @method static Widget invokeWithFallback($fallback, string $widgetUid, string $status)
 */
class EnableWidget extends Task
{
    /**
     * @var bool
     */
    protected $status;

    /**
     * @var string
     */
    protected $widgetUid;

    /**
     * constructor.
     *
     * @param string $widgetUid
     * @param string $status
     */
    public function __construct(string $widgetUid, string $status)
    {
        $this->widgetUid = $widgetUid;
        $this->status    = $status;
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
     */
    public function execute()
    {
        $widget = Widget::byUID($this->widgetUid);

        $widget->enabled = (bool)$this->status;

        Exception::throwUnless($widget->update(), 'Could not enable the widget');

        return $widget;
    }
}
