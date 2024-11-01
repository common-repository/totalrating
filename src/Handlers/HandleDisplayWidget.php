<?php

namespace TotalRating\Handlers;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Entities\EntityManager;
use TotalRating\Events\OnDisplayWidget;
use TotalRating\Models\Widget;
use TotalRating\Tasks\Utils\GetAllowedRatingWidgetTags;
use TotalRating\Tasks\Widget\RenderWidget;
use TotalRatingVendors\League\Event\EventInterface;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\ActionHandler;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Modules\Manager;

class HandleDisplayWidget extends ActionHandler
{
    /**
     * @var EntityManager
     */
    protected $resolver;

    /**
     * @var Manager
     */
    protected $manager;

    public function __construct(EntityManager $resolver, Manager $manager)
    {
        $this->resolver = $resolver;
        $this->manager  = $manager;
    }

    /**
     * @param EventInterface|OnDisplayWidget $event
     */
    public function handle(EventInterface $event)
    {
        try {
            $entity = $this->resolver->resolveFromContext($event->widgetUid);
            $widget = Widget::byUidAndActive($event->widgetUid);
            echo wp_kses(RenderWidget::invoke($this->manager, $widget, $entity), GetAllowedRatingWidgetTags::invoke());
        } catch (Exception $e) {
            echo '';
        }
    }
}
