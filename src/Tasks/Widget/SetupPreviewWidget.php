<?php


namespace TotalRating\Tasks\Widget;
! defined( 'ABSPATH' ) && exit();



use Throwable;
use TotalRatingVendors\TotalSuite\Foundation\Task;

class SetupPreviewWidget extends Task
{
    protected function validate()
    {
        return true;
    }

    protected function execute()
    {
        add_filter('template_include', [$this, 'handleTemplateRedirect']);
    }

    public function handleTemplateRedirect($template)
    {
        if ($widgetUid = get_query_var('widget_uid')) {
            try {
                echo PreviewWidget::invoke($widgetUid);
            } catch (Throwable $exception) {
                wp_die($exception->getMessage(), get_bloginfo('name'));
            }

            exit;
        }

        return $template;
    }
}
