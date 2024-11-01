<?php

namespace TotalRating\Tasks\Widget;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Entities\Entity;
use TotalRating\Models\Widget;
use TotalRating\Plugin;
use TotalRating\Tasks\Utils\GetAllowedRatingWidgetTags;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Helpers\Html;
use TotalRatingVendors\TotalSuite\Foundation\Task;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Modules\Manager;

/**
 * Class RenderWidget
 *
 * @package TotalRating\Tasks\Widget
 * @method static string invoke(Manager $manager, Widget $widget, Entity $entity = null)
 * @method static string invokeWithFallback($fallback, Manager $manager, Widget $widget, Entity $entity = null)
 */
class RenderWidget extends Task
{
    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var Widget
     */
    protected $widget;

    /**
     * @var Entity
     */
    protected $entity;

    /**
     * constructor.
     *
     * @param  Manager  $manager
     * @param  Widget  $widget
     * @param  Entity|null  $entity
     *
     * @throws Exception
     */
    public function __construct(Manager $manager, Widget $widget, Entity $entity = null)
    {
        if ($entity === null) {
            $entity = new Entity($widget->id, $widget->name, 'widget', null, null);
        }

        $this->widget  = $widget;
        $this->manager = $manager;
        $this->entity  = $entity;
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
        // Prepare widget
        $widget = $this->widget->toPublic($this->entity);

        // Prepare template
        $template = $this->widget->getTemplate();
        $renderer = $this->manager->loadTemplate($template);

        // Check limitations
        if ($this->widget->isLimitationByUserEnabled() && $this->widget->isResultsHidden() && !is_user_logged_in()) {
            return '';
        }

        $entityUid = $this->entity->getUid();

        $wrapper = Html::create(
            'div',
            [
                'id'              => "totalrating-widget-{$widget->uid}-{$entityUid}",
                'data-entity-uid' => $entityUid,
                'data-widget-uid' => $widget->uid,
                'class'           => 'totalrating-widget-wrapper',
                'data-template'   => $template,
            ],
            Html::create(
                'div',
                ['class' => 'totalrating-loading',],
                Html::create('div', ['class' => 'totalrating-loading-spinner',])
            )
        );

        wp_enqueue_style('totalrating-loading');

        $callback = function () use ($widget, $renderer) {
            echo wp_kses($renderer->render($widget, $this->entity), GetAllowedRatingWidgetTags::invoke());
        };

        add_action('wp_footer', $callback); // Frontend
        add_action('admin_footer', $callback); // Backoffice

        if (Plugin::options('general.showCredits', false)) {
            $credit = Html::create(
                'div',
                [
                    'class' => 'totalrating-credits',
                    'style' => 'font-family: sans-serif; font-size: 9px; text-transform: uppercase;text-align: center; padding: 10px 0;',
                ],
                sprintf(
                    esc_html__('Powered by %s', 'totalrating'),
                    'TotalRating'
                )
            );

            return $wrapper->addContent($credit)->render();
        }

        if (wp_doing_ajax()) {
            $wrapper = Html::create('', [], [
                $wrapper,
                wp_kses($renderer->render($widget, $this->entity), GetAllowedRatingWidgetTags::invoke()),
            ]);
        }

        return $wrapper->render();
    }
}
