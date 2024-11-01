<?php

namespace TotalRating;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Admin\EntityFilter;
use TotalRating\Capabilities\UserCanCreateWidget;
use TotalRating\Capabilities\UserCanDeleteWidget;
use TotalRating\Capabilities\UserCanExportData;
use TotalRating\Capabilities\UserCanManageModules;
use TotalRating\Capabilities\UserCanManageOptions;
use TotalRating\Capabilities\UserCanUpdateWidget;
use TotalRating\Capabilities\UserCanViewData;
use TotalRating\Capabilities\UserCanViewWidgets;
use TotalRating\Entities\EntityManager;
use TotalRating\Events\OnDisplayResults;
use TotalRating\Events\OnDisplayWidget;
use TotalRating\Events\OnRoutesRegistered;
use TotalRating\Handlers\HandleDisplayResults;
use TotalRating\Handlers\HandleDisplayWidget;
use TotalRating\Pages\Dashboard;
use TotalRating\Shortcodes\Widget;
use TotalRating\Tasks\Widget\RegisterRewriteRules;
use TotalRating\Tasks\Widget\SetupPreviewWidget;
use TotalRating\Tasks\Widget\SetupWidgetsFromContext;
use TotalRatingVendors\League\Container\ServiceProvider\ServiceProviderInterface;
use TotalRatingVendors\TotalSuite\Foundation\Database\Connection;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\DatabaseException;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Migration\Migrator;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Actions\Activation\Activate;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Actions\Activation\Check;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Actions\Activation\Unlink;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Actions\Dashboard\Blog as GetBlogFeed;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Actions\Marketing\NPS;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Actions\Modules\Activate as ActivateModule;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Actions\Modules\Deactivate as DeactivateModule;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Actions\Modules\Index as GetModules;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Actions\Modules\Install as InstallModule;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Actions\Modules\Store;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Actions\Modules\Uninstall as UninstallModule;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Actions\Onboarding\Collect;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Actions\Options\Defaults as GetDefaults;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Actions\Options\Get as GetOptions;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Actions\Options\Reset as ResetOptions;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Actions\Options\Update as UpdateOptions;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Actions\Tracking\TrackFeatures;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Actions\Tracking\TrackScreens;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Database\WPConnection;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Modules\Manager;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Options;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Plugin as AbstractPlugin;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Rest\Router;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Roles;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Scheduler;

class Plugin extends AbstractPlugin
{
    protected static $capabilities = [
        UserCanManageOptions::NAME,
        UserCanManageModules::NAME,
        UserCanCreateWidget::NAME,
        UserCanUpdateWidget::NAME,
        UserCanDeleteWidget::NAME,
        UserCanViewWidgets::NAME,
        UserCanViewData::NAME,
        UserCanExportData::NAME,
    ];

    /**
     * Initiate the plugin
     *
     * @throws Exception
     */
    public function run()
    {
        (new Dashboard());
        EntityFilter::instance();

        RegisterRewriteRules::invoke();
        SetupPreviewWidget::invoke();

        SetupWidgetsFromContext::invoke(
            Manager::instance(),
            EntityManager::instance()
        );

        OnDisplayWidget::listen(HandleDisplayWidget::class);
        OnDisplayResults::listen(HandleDisplayResults::class);
    }

    /**
     * @inheritDoc
     */
    public function getServiceProvider(): ServiceProviderInterface
    {
        return new ServiceProvider();
    }

    /**
     * @inheritDoc
     */
    public function registerRoutes(Router $router)
    {
        // Onboarding
        $router->put('/admin/collect', Collect::class)
               ->capability(UserCanManageOptions::class);

        // Widget
        $router->get('/widget', Actions\Widgets\GetPublic::class);
        $router->get('/admin/widget', Actions\Widgets\Index::class)
               ->capability(UserCanViewWidgets::class);
        $router->get('/admin/widget', Actions\Widgets\Get::class)
               ->capability(UserCanViewWidgets::class);
        $router->post('/admin/widget', Actions\Widgets\Create::class)
               ->capability(UserCanCreateWidget::class);
        $router->put('/admin/widget', Actions\Widgets\Update::class)
               ->capability(UserCanUpdateWidget::class);
        $router->delete('/admin/widget', Actions\Widgets\Delete::class)
               ->capability(UserCanDeleteWidget::class);
        $router->delete('/admin/widget/trash', Actions\Widgets\Trash::class)
               ->capability(UserCanDeleteWidget::class);
        $router->patch('/admin/widget/restore', Actions\Widgets\Restore::class)
               ->capability(UserCanDeleteWidget::class);
        $router->patch('/admin/widget/enable', Actions\Widgets\Enable::class)
               ->capability(UserCanUpdateWidget::class);
        $router->patch('/admin/widget/reset', Actions\Widgets\Reset::class)
               ->capability(UserCanDeleteWidget::class);

        // Options
        $router->get('/admin/options', GetOptions::class)
               ->capability(UserCanManageOptions::class);
        $router->post('/admin/options', UpdateOptions::class)
               ->capability(UserCanManageOptions::class);
        $router->delete('/admin/options', ResetOptions::class)
               ->capability(UserCanManageOptions::class);
        $router->get('/admin/options/defaults', GetDefaults::class)
               ->capability(UserCanManageOptions::class);

        // Ratings
        $router->patch('/rating', Actions\Ratings\Change::class);
        $router->post('/rating', Actions\Ratings\Create::class);
        $router->delete('/rating', Actions\Ratings\Revoke::class);
        $router->get('/admin/rating', Actions\Ratings\Index::class)->capability(UserCanViewData::class);
        $router->get('/admin/rating', Actions\Ratings\Get::class)->capability(UserCanViewData::class);
        $router->post('/admin/rating/export', Actions\Ratings\Export::class)->capability(UserCanExportData::class);
        $router->delete('/admin/rating/delete', Actions\Ratings\Delete::class)->capability(UserCanDeleteWidget::class);

        // Modules
        $router->get('/admin/modules', GetModules::class)
               ->capability(UserCanManageModules::class);
        $router->post('/admin/modules/install', InstallModule::class)
               ->capability(UserCanManageModules::class);
        $router->patch('/admin/modules/activate', ActivateModule::class)
               ->capability(UserCanManageModules::class);
        $router->patch('/admin/modules/deactivate', DeactivateModule::class)
               ->capability(UserCanManageModules::class);
        $router->delete('/admin/modules/uninstall', UninstallModule::class)
               ->capability(UserCanManageModules::class);
        $router->get('/admin/modules/store', Store::class)
               ->capability(UserCanManageModules::class);

        // Dashboard
        $router->get('/admin/dashboard/blog', GetBlogFeed::class)
               ->capability(UserCanViewData::class);
        $router->get('/admin/dashboard/activity', Actions\Dashboard\Activity::class)
               ->capability(UserCanViewData::class);
        $router->get('/admin/dashboard/insights', Actions\Dashboard\Insights::class)
               ->capability(UserCanViewData::class);

        // Marketing
        $router->post('/admin/marketing/nps', NPS::class)
               ->capability(UserCanManageOptions::class);

        // Tracking
        $router->post('/admin/tracking/features', TrackFeatures::class)
               ->capability(UserCanViewData::class);
        $router->post('/admin/tracking/screens', TrackScreens::class)
               ->capability(UserCanViewData::class);

        // Activation
        $router->post('/admin/activation', Activate::class)
               ->capability(UserCanViewData::class);

        $router->get('/admin/license', Check::class)
               ->capability(UserCanViewData::class);

        $router->post('/admin/license/unlink', Unlink::class)
               ->capability(UserCanViewData::class);

        OnRoutesRegistered::emit($router);
    }

    /**
     * @inheritDoc
     */
    public function registerShortCodes()
    {
        new Widget();
    }

    /**
     * @inheritDoc
     */
    public function registerWidgets()
    {
    }

    /**
     * @inheritDoc
     */
    public function registerAssets()
    {
        $version          = Plugin::env('version');
        $baseUrl          = static::env('url.base');
        $lazyLoadEnabled  = static::options('advanced.lazyLoad.enabled');
        $suffix           = Plugin::env()
                                  ->isDebug() ? '.min' : '';

        // Enqueue vendors
        wp_register_script(
            'vue-js',
            $baseUrl."/assets/js/vue{$suffix}.js",
            ['jquery'],
            null
        );

        if ($lazyLoadEnabled) {
            wp_add_inline_script('vue-js', 'var lazyLoadRatingWidgets = true;', 'before');
        }

        wp_register_style('totalrating-loading', $baseUrl."/assets/css/loading.css", [], $version);
    }

    /**
     * @inheritDoc
     */
    public function onActivation()
    {
        /**
         * @var Migrator
         */
        Migrator::instance()
                ->execute();

        // Capabilities
        Roles::set(
            Roles::ADMINISTRATOR,
            static::$capabilities
        );

        flush_rewrite_rules(true);
    }

    /**
     * @inheritDoc
     */
    public function onDeactivation()
    {
        // Capabilities
        Roles::remove(
            Roles::ADMINISTRATOR,
            static::$capabilities
        );

        Scheduler::instance()
                 ->unregister();
    }

    /**
     * @inheritDoc
     * @noinspection PhpRedundantCatchClauseInspection
     */
    public static function onUninstall()
    {
        /**
         * @var bool $wipeData
         * @var WPConnection $db
         * @var Options $options
         */
        $wipeData = (bool) static::options('uninstall.wipeOnUninstall', false);
        $db       = static::instance()
                          ->container(Connection::class);

        // Delete plugin options
        delete_option(static::env('stores.optionsKey'));

        delete_option(static::env('stores.modulesKey'));

        delete_option(static::env('stores.versionKey'));

        delete_option(static::env('stores.trackingKey'));

        if ($wipeData) {
            try {
                $db->raw(sprintf('DROP TABLE %stotalrating_widgets', $db->getTablePrefix()));
                $db->raw(sprintf('DROP TABLE %stotalrating_ratings', $db->getTablePrefix()));
            } catch (DatabaseException $e) {
                die($e->getMessage());
            }
        }
    }

    /**
     * @return array
     */
    public function objectsCount()
    {
        return [
            'widgets' => Models\Widget::count(),
            'ratings' => Models\Rating::count(),
        ];
    }
}
