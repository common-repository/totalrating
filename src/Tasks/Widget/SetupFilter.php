<?php

namespace TotalRating\Tasks\Widget;
! defined( 'ABSPATH' ) && exit();



use TotalRating\Entities\Entity;
use TotalRating\Models\Widget;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\DatabaseException;
use TotalRatingVendors\TotalSuite\Foundation\Support\Collection;
use TotalRatingVendors\TotalSuite\Foundation\Task;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Modules\Manager;

/**
 * Class SetupFilter
 *
 * @package TotalRating\Tasks\Widget
 * @method static Collection invoke(Manager $manager, Entity $entity)
 * @method static Collection invokeWithFallback($fallback, Manager $manager, Entity $entity)
 */
class SetupFilter extends Task
{
    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var Entity
     */
    protected $entity;

    /**
     * constructor.
     *
     * @param Manager $manager
     * @param Entity  $entity
     */
    public function __construct(Manager $manager, Entity $entity)
    {
        $this->entity  = $entity;
        $this->manager = $manager;
    }

    /**
     * @inheritDoc
     */
    protected function validate()
    {
        return true;
    }

    /**
     * @return Collection
     * @throws DatabaseException
     */
    protected function execute()
    {
        $entity  = $this->entity;
        $widgets = Widget::byEntityAndActive($this->entity);

        /**
         * @var Widget $widget
         */
        foreach ($widgets->all() as $widget) {
            if ($widget->isAutoIntegrated()) {
                add_filter(
                    'the_content',
                    function ($content) use ($widget, $entity) {
                        $html = RenderWidget::invoke($this->manager, $widget, $entity);

                        if ($widget->isPlacedBeforeContent()) {
                            $content = $html . $content;
                        } else {
                            $content .= $html;
                        }

                        return $content;
                    }
                );
            }
        }

        return $widgets;
    }
}
