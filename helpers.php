<?php
! defined( 'ABSPATH' ) && exit();


use TotalRating\Entities\Entity;
use TotalRating\Entities\EntityManager;
use TotalRating\Models\Widget;
use TotalRating\Plugin;
use TotalRating\Tasks\Widget\RenderWidget;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\DatabaseException;

if (!function_exists('TotalRating')) {
    /**
     * @return Plugin
     */
    function TotalRating()
    {
        return TotalRating\Plugin::instance();
    }
}

if (!function_exists('totalrating_widget')) {
    /**
     * @param int         $id
     * @param bool        $hideTitle
     * @param string      $content
     *
     * @param Entity|null $entity
     *
     * @return string
     * @throws DatabaseException
     */
    function totalrating_widget($id, $hideTitle = false, $content = '', Entity $entity = null)
    {
        if ($entity === null) {
            /**
             * @var EntityManager $resolver
             */
            $resolver = Plugin::get(EntityManager::class);
            $entity   = $resolver->resolveFromContext();
        }


        $widget = Widget::byIdAndActive($id);

        if (!$widget) {
            return '';
        }

        if ($hideTitle) {
            $widget->deleteAttribute('title');
        }

        if (!empty($content)) {
            $widget->setAttribute('description', esc_html($content));
        }

        /**
         * @var TotalRatingVendors\TotalSuite\Foundation\WordPress\Modules\Manager $manager
         */
        $manager = Plugin::get(TotalRatingVendors\TotalSuite\Foundation\WordPress\Modules\Manager::class);

        return (new RenderWidget($manager, $widget->withPublicStatistics($entity), $entity))->run();
    }
}
