<?php

namespace TotalRating\Templates\DefaultTemplate;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Entities\Entity;
use TotalRating\Models\Widget;
use TotalRating\Plugin;
use TotalRating\Template;

class Module extends Template
{
    protected function registerScripts()
    {
        // Enqueue main script
        wp_enqueue_script(
            'default-template',
            Plugin::env()->isDebug() ? $this->getUrl('assets/js/app.js') : $this->getUrl('assets/js/app.min.js'),
            ['vue-js'],
            Plugin::env('version'),
            true
        );
    }

    /**
     * @param Widget $widget
     * @param Entity|null $entity
     * @param string $template
     *
     * @return string
     */
    public function render(Widget $widget, Entity $entity = null, $template = 'widget'): string
    {
        $this->registerScripts();
        return parent::render($widget, $entity, $template);
    }

    /**
     * @param Widget $widget
     * @param Entity|null $entity
     * @param string $template
     *
     * @return string
     */
    public function renderResults(Widget $widget, Entity $entity = null, $template = 'results'): string
    {
        $this->registerScripts();
        return parent::render($widget, $entity, $template);
    }
}
