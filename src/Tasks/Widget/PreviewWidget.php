<?php

namespace TotalRating\Tasks\Widget;
! defined( 'ABSPATH' ) && exit();



use TotalRating\Capabilities\UserCanUpdateWidget;
use TotalRating\Models\Widget;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Task;
use TotalRatingVendors\TotalSuite\Foundation\View\Engine;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Modules\Manager;

class PreviewWidget extends Task
{
    /**
     * @var string
     */
    protected $widgetUid;

    /**
     * PreviewPreset constructor.
     *
     * @param  string  $widgetUid
     */
    public function __construct(string $widgetUid)
    {
        $this->widgetUid = $widgetUid;
    }

    /**
     * @return mixed|void
     * @throws Exception
     */
    protected function validate()
    {
        Exception::throwUnless(
            UserCanUpdateWidget::check(),
            esc_html__('Sorry you are not allowed to preview this widget.', 'totalrating')
        );
    }

    /**
     * @return mixed|void
     * @throws Exception
     * @throws Exception
     */
    protected function execute()
    {
        $widget = Widget::byUID($this->widgetUid)->fromPreviewContext();

        return ViewWidget::invoke(Manager::instance(), Engine::instance(), $widget);
    }
}
