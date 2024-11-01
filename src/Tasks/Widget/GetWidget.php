<?php


namespace TotalRating\Tasks\Widget;
! defined( 'ABSPATH' ) && exit();



use TotalRating\Models\Widget;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Task;

/**
 * Class GetWidget
 *
 * @package TotalRating\Tasks\Widget
 * @method static Widget invoke(string $widgetUid)
 * @method static Widget invokeWithFallback($fallback, string $widgetUid)
 */
class GetWidget extends Task
{
    /**
     * @var string
     */
    protected $widgetUid;

    /**
     * constructor.
     *
     * @param string $widgetUid
     */
    public function __construct(string $widgetUid)
    {
        $this->widgetUid = $widgetUid;
    }

    /**
     * @inheritDoc
     */
    protected function validate()
    {
        return true;
    }

    /**
     * @return Widget
     * @throws Exception
     */
    protected function execute(): Widget
    {
        return Widget::byUID($this->widgetUid);
    }
}
