<?php

namespace TotalRating\Pages;
! defined( 'ABSPATH' ) && exit();


use Exception;
use TotalRating\Capabilities\UserCanManageOptions;
use TotalRating\Events\OnBackofficeAssetsEnqueued;
use TotalRating\Plugin;
use TotalRating\Services\WorkflowRegistry;
use TotalRating\Tasks\GetExpressions;
use TotalRating\Tasks\GetLanguages;
use TotalRating\Tasks\GetPresets;
use TotalRating\Tasks\GetRoles;
use TotalRating\Tasks\Widget\GetWidgetDefaults;
use TotalRatingVendors\TotalSuite\Foundation\License;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Admin\Page;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Tasks\Promotion\GetModules;

class Dashboard extends Page
{
    public function register()
    {
        parent::register();
        $slug    = $this->slug();
        $submenu = [
            "{$slug}#/dashboard"         => esc_html__('Dashboard', 'totalrating'),
            "{$slug}#/widgets"           => esc_html__('Widgets', 'totalrating'),
            "{$slug}#/modules/template"  => esc_html__('Templates', 'totalrating'),
            "{$slug}#/modules/extension" => esc_html__('Extensions', 'totalrating'),
            "{$slug}#/options"           => esc_html__('Options', 'totalrating'),
            "{$slug}#/popup"             => esc_html__('Popup', 'totalrating'),
            "{$slug}#/support"           => esc_html__('Support', 'totalrating'),
            "{$slug}#/license"           => esc_html__('License', 'totalrating'),
        ];
        foreach ($submenu as $slug => $label) {
            add_submenu_page(
                $this->slug(),
                $label,
                $label,
                $this->capability(),
                $slug,
                [$this, 'render']
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function assets()
    {
        // Disable emoji
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('admin_print_styles', 'print_emoji_styles');

        $baseUrl = Plugin::env('url.base', '');

        OnBackofficeAssetsEnqueued::emit($baseUrl);

        wp_enqueue_style('material-font', 'https://fonts.googleapis.com/icon?family=Material+Icons');
        wp_enqueue_script('runtime', $baseUrl.'/assets/backoffice/runtime.js');
        wp_enqueue_script('polyfills', $baseUrl.'/assets/backoffice/polyfills-es5.js');
        wp_enqueue_script('vendor', $baseUrl.'/assets/backoffice/vendor.js');
        wp_enqueue_script('styles', $baseUrl.'/assets/backoffice/styles.js');
        wp_enqueue_script('main', $baseUrl.'/assets/backoffice/main.js', [], false, true);
        wp_localize_script('main', 'APP_CONFIG', $this->getConfig());
    }

    /**
     * @inheritDoc
     */
    public function icon(): string
    {
        return 'dashicons-star-half';
    }

    /**
     * @inheritDoc
     */
    public function capability(): string
    {
        return UserCanManageOptions::NAME;
    }

    /**
     * @inheritDoc
     */
    public function title(): string
    {
        return esc_html__('TotalRating', 'totalrating');
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function data(): array
    {
        return [
            'baseUrl'  => Plugin::env('url.base'),
            'config'   => $this->getConfig(),
            'basePath' => Plugin::env('path.base'),
        ];
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getConfig(): array
    {
        $baseUrl    = Plugin::env('url.base').'/';
        $apiBaseUrl = rest_url('totalrating');
        $wpBaseUrl  = rest_url();
        $wpNonce    = wp_create_nonce('wp_rest');

        $entities = [
            'post' => [
                ['id' => 'post:post', 'label' => 'Post', 'icon' => 'description'],
                ['id' => 'post:page', 'label' => 'Page', 'icon' => 'class'],
            ],
        ];

        $postTypes = [
            [
                'icon'  => 'select_all',
                'name'  => 'Site-wide',
                'value' => 'all',
            ],
            [
                'icon'  => 'home',
                'name'  => 'Home',
                'value' => 'home',
            ],
            [
                'icon'  => 'description',
                'name'  => 'Post',
                'value' => 'post',
            ],
            [
                'icon'  => 'class',
                'name'  => 'Page',
                'value' => 'page',
            ],
        ];

        foreach ((array) get_post_types(['public' => true, '_builtin' => false], 'objects') as $postType) {
            $entities['post'][] = [
                'id'    => "post:{$postType->name}",
                'label' => $postType->labels->singular_name,
                'icon'  => 'insert_drive_file',
            ];

            $postTypes[] = [
                'icon'  => 'insert_drive_file',
                'name'  => $postType->labels->singular_name,
                'value' => $postType->name,
            ];
        }

        return [
            'baseUrl'     => $baseUrl,
            'api'         => [
                'wp'    => $wpBaseUrl,
                'base'  => $apiBaseUrl,
                'nonce' => $wpNonce,
            ],
            'presets'     => GetPresets::invoke(),
            'entities'    => $entities,
            'postTypes'   => $postTypes,
            'defaults'    => GetWidgetDefaults::invoke(),
            'workflows'   => WorkflowRegistry::instance()
                                             ->toArray(),
            'languages'   => GetLanguages::invoke(),
            'expressions' => GetExpressions::invoke(),
            'currentUser' => [
                'name'  => wp_get_current_user()->display_name,
                'email' => wp_get_current_user()->user_email,
            ],
            'support'     => [
                'url'           => 'https://totalsuite.net/support/?utm_source=support-panel&utm_medium=in-app&utm_campaign=totalrating',
                'documentation' => 'https://totalsuite.net/product/totalrating/documentation/?utm_source=support-panel&utm_medium=in-app&utm_campaign=totalrating',
                'search'        => 'https://totalsuite.net/search/?utm_source=support-panel&utm_medium=in-app&utm_campaign=totalrating',
                'sections'      => [
                    [
                        'title' => 'Get started',
                        'items' => [
                            [
                                'title' => 'How to install TotalRating',
                                'url'   => 'https://totalsuite.net/documentation/totalrating/basics-totalrating/how-to-install-totalrating/?utm_source=support-panel&utm_medium=in-app&utm_campaign=totalrating',
                            ],
                            [
                                'title' => 'How to create a widget',
                                'url'   => 'https://totalsuite.net/documentation/totalrating/basics-totalrating/how-to-create-a-widget/?utm_source=support-panel&utm_medium=in-app&utm_campaign=totalrating',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Take a step further',
                        'items' => [
                            [
                                'title' => 'How to integrate a widget',
                                'url'   => 'https://totalsuite.net/documentation/totalrating/basics-totalrating/how-to-integrate-a-widget/?utm_source=support-panel&utm_medium=in-app&utm_campaign=totalrating',
                            ],
                            [
                                'title' => 'How to customize appearance',
                                'url'   => 'https://totalsuite.net/documentation/totalrating/basics-totalrating/how-to-customize-appearance/?utm_source=support-panel&utm_medium=in-app&utm_campaign=totalrating',
                            ],
                        ],
                    ],
                ],
            ],
            'modules'     => GetModules::invoke(),
            'roles'       => GetRoles::invoke(),
            'product'     => Plugin::env('product', []),
            'customer'    => Plugin::options(
                'customer',
                [
                    "status"     => "init",
                    "email"      => "",
                    "audience"   => "other",
                    "usage"      => "other",
                    "tracking"   => true,
                    "newsletter" => false,
                ]
            ),
            'onboarding'  => [
                'url'   => [
                    'documentation' => 'https://totalsuite.net/product/totalrating/documentation/',
                    'store'         => 'https://totalsuite.net/product/totalrating/add-ons/',
                ],
                'steps' => [
                    'welcome'      => [
                        'title' => esc_html__('Hey mate!', 'totalrating'),
                        'text'  => esc_html__(
                            "We are delighted to see you started using TotalRating, \n TotalRating will impress you, we promise!",
                            'totalrating'
                        ),
                        'tabs'  => [
                            [
                                'icon'  => 'touch_app',
                                'title' => esc_html__('User Friendly', 'totalrating'),
                                'text'  => esc_html__(
                                    "Easily create a rating widget within a few seconds.",
                                    'totalrating'
                                ),
                            ],
                            [
                                'icon'  => 'style',
                                'title' => esc_html__('Elegant Design', 'totalrating'),
                                'text'  => esc_html__(
                                    "Achieve a better response rate using an attractive rating widget.",
                                    'totalrating'
                                ),
                            ],
                            [
                                'icon'  => 'power',
                                'title' => esc_html__('Flexibility & Extensibility', 'totalrating'),
                                'text'  => esc_html__(
                                    "Create rating widgets that are highly flexible and extensible.",
                                    'totalrating'
                                ),
                            ],
                        ],
                    ],
                    'introduction' => [
                        'title' => esc_html__('Get started', 'totalrating'),
                        'text'  => esc_html__(
                            "We've prepared some materials for you to ease your learning curve.",
                            'totalrating'
                        ),
                        'posts' => [
                            [
                                'title' => esc_html__("How to create a widget", "totalrating"),
                                'text'  => esc_html__(
                                    "Learn how to create a widget in no time using TotalRating.",
                                    "totalrating"
                                ),
                                'image' => "assets/images/onboarding/create.svg",
                                'url'   => "https://totalsuite.net/documentation/totalrating/basics-totalrating/how-to-create-a-widget/",
                            ],
                            [
                                'title' => esc_html__("How to integrate a widget", "totalrating"),
                                'text'  => esc_html__(
                                    "Learn how to integrate the rating widget on different areas on your website.",
                                    "totalrating"
                                ),
                                'image' => "assets/images/onboarding/integrate.svg",
                                'url'   => "https://totalsuite.net/documentation/totalrating/basics-totalrating/how-to-integrate-a-widget/",
                            ],
                            [
                                'title' => esc_html__("How to customize appearance", "totalrating"),
                                'text'  => esc_html__(
                                    "Learn how to customize the appearance of the rating widget to match your brand.",
                                    "totalrating"
                                ),
                                'image' => "assets/images/onboarding/integrate.svg",
                                'url'   => "https://totalsuite.net/documentation/totalrating/basics-totalrating/how-to-customize-appearance/",
                            ],
                        ],
                    ],
                    'connect'      => [
                        'title'       => esc_html__('Get started', 'totalrating'),
                        'text'        => esc_html__(
                            "We've prepared some materials for you to ease your learning curve.",
                            'totalrating'
                        ),
                        'information' => [
                            esc_html__('Let you know about the upcoming features.', 'totalrating'),
                            esc_html__('Inform you about important updates.', 'totalrating'),
                            esc_html__('Adjust recommendations.', 'totalrating'),
                            esc_html__('Adapt product settings.', 'totalrating'),
                            esc_html__('Send you exclusive offers.', 'totalrating'),
                        ],
                    ],
                    'finish'       => [
                        'title'       => esc_html__('Bravo! You did it!', 'totalrating'),
                        'text'        => esc_html__(
                            "You are all set to start making informed decisions! One last thing, we'd like to collect some anonymous usage information that will help us shape up TotalRating.",
                            'totalrating'
                        ),
                        'information' => [
                            esc_html__('Make TotalRating stable and bug-free.', 'totalrating'),
                            esc_html__('Get an overview of environments.', 'totalrating'),
                            esc_html__('Optimize performance.', 'totalrating'),
                            esc_html__('Adjust default parameters.', 'totalrating'),
                        ],
                    ],
                ],
            ],
            'license'     => License::instance()->toArray(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function template(): string
    {
        return 'dashboard';
    }
}
