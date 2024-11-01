<?php

namespace TotalRating\Tasks\Widget;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Filters\FilterWidgetUrlStructure;
use TotalRating\Models\Widget;
use TotalRatingVendors\TotalSuite\Foundation\Task;

/**
 * Class RegisterRewriteRules
 *
 * @package TotalRating\Tasks\Widget
 */
class RegisterRewriteRules extends Task
{

    /**
     * @inheritDoc
     */
    protected function validate()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function execute()
    {
        add_filter(
            'query_vars',
            static function ($queryVars) {
                $queryVars[] = 'widget_uid';

                return $queryVars;
            }
        );
        add_action(
            'init',
            static function () {
                $structure = FilterWidgetUrlStructure::apply(Widget::URL_BASE . "/([a-z0-9-]+)[/]?$");
                add_rewrite_rule($structure, 'index.php?widget_uid=$matches[1]', 'top');
                flush_rewrite_rules();
            }
        );
    }
}
