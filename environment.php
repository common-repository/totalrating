<?php
! defined( 'ABSPATH' ) && exit();


return [
    'version'    => '1.8.5',
    'loader'     => (require 'vendor/autoload.php'),
    'textdomain' => 'totalrating',
    'product'    => [
        'id'   => 'totalrating',
        'name' => 'TotalRating',
        'url'  => 'https://totalsuite.net/products/totalrating',
    ],
    'namespaces' => [
        'rest'      => 'totalrating',
        'extension' => 'TotalRating\\Extensions',
        'template'  => 'TotalRating\\Templates',
    ],
    'path'       => [
        'base'        => wp_normalize_path(plugin_dir_path(__FILE__)),
        'languages'   => wp_normalize_path(plugin_dir_path(__FILE__)) . 'languages',
        'uploads'     => wp_get_upload_dir()['basedir'],
        'modules'     => wp_normalize_path(plugin_dir_path(__FILE__) . 'modules'),
        'userModules' => wp_normalize_path(wp_get_upload_dir()['basedir'] . '/totalrating/modules'),
        'migrations'  => wp_normalize_path(plugin_dir_path(__FILE__)) . 'migrations',
    ],
    'url'        => [
        'base'        => plugins_url('', __FILE__),
        'apiBase'     => '/totalrating',
        'modules'     => [
            'base'  => plugins_url('modules', __FILE__),
            'store' => 'https://totalsuite.net/api/v3/modules?for=totalrating',
        ],
        'userModules' => [
            'base' => wp_get_upload_dir()['baseurl'] . '/totalrating/modules',
        ],
        'blogFeed'    => 'https://totalsuite.net/wp-json/wp/v2/blog_article',
        'tracking'    => [
            'nps'         => 'https://collect.totalsuite.net/nps',
            'uninstall'   => 'https://collect.totalsuite.net/uninstall',
            'environment' => 'https://collect.totalsuite.net/env',
            'events'      => 'https://collect.totalsuite.net/event',
            'log'         => 'https://collect.totalsuite.net/log',
            'onboarding'  => 'https://collect.totalsuite.net/onboarding',
        ],
    ],
    'db'         => [
        'prefix' => $GLOBALS['wpdb']->prefix,
    ],
    'stores'     => [
        'optionsKey'  => 'totalrating_options',
        'modulesKey'  => 'totalrating_modules',
        'versionKey'  => 'totalrating_version',
        'trackingKey' => 'totalrating_tracking'
    ],
    'defaults'   => [
        'options' => \TotalRating\Tasks\Options\GetDefaultOptions::invoke(),
    ]
];
