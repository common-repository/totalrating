<?php

namespace TotalRating\Shortcodes;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Entities\Entity;
use TotalRating\Entities\EntityManager;
use TotalRating\Models\Widget as WidgetModel;
use TotalRating\Plugin;
use TotalRating\Tasks\Widget\RenderWidget;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\DatabaseException;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Modules\Manager;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Shortcode;

class Widget extends Shortcode
{
    public function __construct()
    {
        parent::__construct('totalrating-widget');
    }

    /**
     * @return string
     * @throws DatabaseException|Exception
     */
    public function render(): string
    {
        $widget = WidgetModel::byIdAndActive($this->getAttribute('id'));

        if (!$widget) {
            return '';
        }

        if ($this->getAttribute('entity-id')) {
            $entity = new Entity(
                $this->getAttribute('entity-id'),
                $this->getAttribute('entity-title'),
                $this->getAttribute('entity-type'),
                $this->getAttribute('entity-type-name'),
                ''
            );
        } else {
            $entity = EntityManager::instance()->resolveFromContext($widget->uid);
        }

        if ($this->getAttribute('hide-title')) {
            $widget->deleteAttribute('title');
        }

        if ($this->getContent()) {
            $widget->setAttribute('description', $this->getContent());
        }

        return RenderWidget::invoke(Plugin::get(Manager::class), $widget, $entity);
    }
}
