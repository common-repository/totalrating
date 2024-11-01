<?php
! defined( 'ABSPATH' ) && exit();
 /**
 * @var Widget $widget
 * @var string $content
 */

use TotalRating\Models\Widget;

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> <?php echo is_admin_bar_showing() ? 'with-admin-bar' : 'without-admin-bar'; ?>>
<head>
    <meta charset="<?php echo esc_attr(get_bloginfo('charset')); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php echo esc_attr(get_bloginfo('pingback_url')); ?>">

    <title><?php wp_title(); ?></title>
    <?php wp_head(); ?>

    <style type="text/css">
        html {
            height: 100%;
            overflow: auto;
        }

        body::before, body::after {
            display: none !important;
        }

        @media screen and (max-width: 782px) {
            html[with-admin-bar] {
                height: calc(100% - 46px);
            }
        }

        body {
            background: #ffffff !important;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100%;
        }


        .totalrating-content {
            margin: 0;
            max-width: 420px;
            width: 100%;
        }

        .totalrating-warning {
            position: absolute;
            z-index: 10;
            top: 0;
            left: 0;
            right: 0;
            padding: 15px;
            background: #EF6C00;
            color: #FFFFFF;
            text-align: center;
            box-shadow: 0 1px 8px rgba(0, 0, 0, 0.1);
        }

        html[with-admin-bar] .totalrating-warning {
            top: 32px;
        }

        @media screen and (max-width: 782px) {


            html[with-admin-bar] .totalrating-warning {
                top: 46px;
            }

            .totalrating-content {
                border-radius: 0;
                margin: 24px;
            }
        }
    </style>
</head>

<body <?php body_class(); ?>>

<?php if (!$widget->enabled): ?>
    <p class="totalrating-warning"><?php esc_html_e('This widget is disabled. You need to enable it to make it publicly accessible.', 'totalrating'); ?></p>
<?php endif; ?>

<main class="totalrating-content">
    <?php echo wp_kses($content, \TotalRating\Tasks\Utils\GetAllowedRatingWidgetTags::invoke()); ?>
</main>

<?php wp_footer(); ?>

</body>
</html>
