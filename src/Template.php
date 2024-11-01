<?php

namespace TotalRating;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Entities\Entity;
use TotalRating\Filters\FilterWidgetCustomCss;
use TotalRating\Models\Widget;
use TotalRatingVendors\League\Container\Container;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Modules\Definition;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Modules\Template as BaseTemplate;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Options;

abstract class Template extends BaseTemplate
{
    const WIDGET_VIEW = 'widget';

    /**
     * @var Options
     */
    protected $options;

    public function __construct(Definition $definition, Container $container)
    {
        parent::__construct($definition, $container);
        $this->options = $this->container->get(Options::class);
    }

    /**
     * @param  Widget  $widget
     *
     * @param  Entity|null  $entity
     *
     * @param  string  $template
     *
     * @return string
     */
    public function render(Widget $widget, Entity $entity = null, $template = self::WIDGET_VIEW): string
    {
        // reCaptcha integration
        if ($this->options->get('advanced.recaptcha.enabled', false)) {
            wp_enqueue_script(
                'recaptcha-v3',
                'https://www.google.com/recaptcha/api.js?render='.$this->options->get('advanced.recaptcha.key'),
                [],
                null
            );
        }

        // Generate nonce
        $nonce = is_user_logged_in() ? wp_create_nonce('wp_rest') : null;

        // Share data
        $this->engine->addData(
            [
                'widget'    => $widget,
                'options'   => [
                    'recaptcha' => [
                        'enabled' => $this->options->get('advanced.recaptcha.enabled'),
                        'key'     => $this->options->get('advanced.recaptcha.key'),
                    ],
                ],
                'language'  => get_locale(),
                'entity'    => $entity,
                'nonce'     => $nonce,
                'apiBase'   => rest_url(Plugin::env('url.apiBase')),
                'customCss' => FilterWidgetCustomCss::apply($widget->getSettings('design.customCss', '')),
                'before'    => '',
                'after'     => '',
                'assets'    => [
                    'css' => [
                        'template' => $this->getUrl('assets/css/style.css'),
                    ],
                    'js'  => [],
                ],
            ]
        );

        return $this->view($template);
    }
}
