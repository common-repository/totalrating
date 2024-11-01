<?php
! defined( 'ABSPATH' ) && exit();

/**
 * @var WP_Post $post
 * @var string[] $widgets
 * @var string $class
 * @var string $position
 */

$widgetsCount = count($widgets);
?>

<div id="totalrating_metabox" class="<?php esc_attr_e($class); ?>">
    <div class="totalrating_metabox_container">
        <?php foreach ($widgets as $widget) : ?>
            <?php echo wp_kses($widget, \TotalRating\Tasks\Utils\GetAllowedRatingWidgetTags::invoke()); ?>
        <?php endforeach ?>
    </div>
</div>
