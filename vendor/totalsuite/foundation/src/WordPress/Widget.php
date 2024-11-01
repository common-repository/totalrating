<?php

namespace TotalRatingVendors\TotalSuite\Foundation\WordPress;
! defined( 'ABSPATH' ) && exit();


use TotalPollVendors\TotalCore\Application;
use WP_Widget;

/**
 * Class Widget
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\WordPress
 */
abstract class Widget extends WP_Widget
{
    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget($args, $instance)
    {
        echo $args['before_widget'];
        if (!empty($instance['title'])):
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        endif;

        echo $args['after_widget'];
    }

    /**
     * Outputs the options form on admin
     *
     * @param array $instance The widget options
     */
    public function form($instance)
    {
    }

    /**
     * Processing widget options on save
     *
     * @param array $new_instance The new options
     * @param array $old_instance The previous options
     *
     * @return array
     */
    public function update($new_instance, $old_instance)
    {
        array_walk($new_instance, 'strip_tags');

        return $new_instance;
    }
}
