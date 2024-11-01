<?php

namespace TotalRating\Tasks\Widget;
! defined( 'ABSPATH' ) && exit();


use Exception;
use TotalRating\Models\Widget;
use TotalRatingVendors\League\Plates\Engine;
use TotalRatingVendors\TotalSuite\Foundation\Task;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Modules\Manager;

/**
 * Class ViewWidget
 *
 * @package TotalRating\Tasks\Widget
 */
class ViewWidget extends Task
{
    /**
     * @var string
     */
    protected $widget;

    /**
     * @var Engine
     */
    protected $engine;

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * ViewSuvey constructor.
     *
     * @param  Manager  $manager
     * @param  Engine  $engine
     * @param  Widget  $widget
     */
    public function __construct(Manager $manager, Engine $engine, Widget $widget)
    {
        $this->widget = $widget;
        $this->manager = $manager;
        $this->engine = $engine;
    }

    /**
     * @inheritDoc
     */
    protected function validate()
    {
        return true;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function execute()
    {
        $renderedWidget = RenderWidget::invoke($this->manager, $this->widget, null);

        // Add widget to the title
        add_filter(
            'wp_title_parts',
            function ($parts) {
                return [$this->widget->name ?: 'Untitled rating widget', get_bloginfo('name')];
            }
        );
        // Add description meta tag
        add_action(
            'wp_head',
            function () {
                if ($this->widget->isPreview()) {
                    show_admin_bar(true);
                }
                printf('<meta name="description" content="%s">'.PHP_EOL, $this->widget->description);
            },
            1
        );

        // Render view
        return $this->engine->render(
            'view',
            ['widget' => $this->widget, 'content' => $renderedWidget]
        );
    }
}
