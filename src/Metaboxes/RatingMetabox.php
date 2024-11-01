<?php

namespace TotalRating\Metaboxes;
! defined( 'ABSPATH' ) && exit();



use TotalRating\Entities\Entity;
use TotalRating\Entities\EntityManager;
use TotalRating\Models\Widget;
use TotalRating\Plugin;
use TotalRating\Tasks\Utils\GetAllowedRatingWidgetTags;
use TotalRating\Tasks\Widget\RenderWidget;
use TotalRatingVendors\League\Plates\Engine;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Helpers\Html;
use TotalRatingVendors\TotalSuite\Foundation\Support\Collection;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Modules\Manager;
use WP_Post;

class RatingMetabox
{
    /**
     * @var Engine
     */
    protected $engine;

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var Widget|Collection
     */
    protected $widgets = null;

    /**
     * @var Entity
     */
    protected $currentEntity = null;

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $position = 'side';

    /**
     * RatingMetabox constructor.
     *
     * @param  Engine  $engine
     * @param  Manager  $manager
     * @param  string  $title
     */
    public function __construct(Engine $engine, Manager $manager, $title)
    {
        $this->engine  = $engine;
        $this->title   = $title;
        $this->manager = $manager;

        $enabled = Plugin::options('advanced.metabox.enabled', true);

        if ($enabled) {
            add_action('add_meta_boxes', [$this, 'register'], 10, 2);
            add_action('admin_enqueue_scripts', [$this, 'assets']);
        }
    }

    public function assets()
    {
        $url = Plugin::env('url.base').'/assets/css/metabox'.(Plugin::env()->isDebug() ? '.css' : '.min.css');

        wp_enqueue_style('totalrating_metabox', $url, []);
    }

    /**
     * @param  string  $post_type
     * @param  WP_Post  $post
     */
    public function register($post_type, $post)
    {
        if (!$post instanceof WP_Post) {
            return;
        }
        
        if (in_array($post->post_status, ['draft', 'auto-draft'], true)) {
            return;
        }

        $this->currentEntity = EntityManager::instance()->resolve($post->ID, 'post:'.$post_type);

        if (!$this->currentEntity) {
            return;
        }

        $this->widgets = Widget::byEntityAndActive($this->currentEntity);

        if ($this->widgets->isEmpty()) {
            return;
        }

        add_meta_box(
            'totalrating_widgets_metabox',
            esc_html__('Ratings', 'totalrating'),
            [$this, 'render'],
            null,
            $this->position,
            'default',
            [
                '__block_editor_compatible_meta_box' => true,
            ]
        );
    }

    /**
     * @param  WP_Post  $post
     *
     * @throws Exception
     */
    public function render(WP_Post $post)
    {
        $widgets = [];

        /**
         * @var Widget $widget
         */
        foreach ($this->widgets as $widget) {
            $widget->withStatistics($this->currentEntity);
            $widget->withSkipToResults()->withMinimalSettings();

            if (empty($widget->title)) {
                $title = empty($widget->name) ? esc_html__('Untitled', 'totalrating') : esc_html($widget->name);
            } else {
                $title = $widget->title;
            }

            $widget->title = '';

            $render = RenderWidget::invoke($this->manager, $widget, $this->currentEntity);

            if (empty($render)) {
                continue;
            }

            $widgets[] = Html::create(
                'div',
                ['class' => 'totalrating_metabox_widget'],
                [
                    Html::create(
                        'a',
                        [
                            'href'  => sprintf(
                                admin_url('admin.php?page=totalrating#/widgets/editor/%s/ratings?entity_id=%s'),
                                $widget->uid,
                                $this->currentEntity->getId()
                            ),
                            'class' => 'totalrating_metabox_widget_title',
                        ],
                        $title
                    ),
                    Html::create('div', ['class' => 'totalrating_metabox_widget_content'], $render),
                ]
            );

            $widget->setAttribute('title', '');
        }

        $widgets = array_filter($widgets);

        if (empty($widgets)) {
            return;
        }

        echo wp_kses(
            $this->engine->render(
                'metaboxes/ratings',
                [
                    'post'     => $post,
                    'widgets'  => $widgets,
                    'position' => $this->position,
                    'class'    => 'totalrating_metabox_'.$this->position,
                ]
            ),
            GetAllowedRatingWidgetTags::invoke()
        );
    }
}
