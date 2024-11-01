<?php

namespace TotalRatingVendors\TotalSuite\Foundation;
! defined( 'ABSPATH' ) && exit();


use Composer\Autoload\ClassLoader;
use TotalRatingVendors\League\Container\Container;
use TotalRatingVendors\League\Container\ServiceProvider\AbstractServiceProvider;
use TotalRatingVendors\League\Container\ServiceProvider\BootableServiceProviderInterface;
use TotalRatingVendors\League\Flysystem\Adapter\AbstractAdapter;
use TotalRatingVendors\Rakit\Validation\Validator;
use TotalRatingVendors\TotalSuite\Foundation\Contracts\CallableResolver;
use TotalRatingVendors\TotalSuite\Foundation\Contracts\ExceptionHandler as ExceptionHandlerContract;
use TotalRatingVendors\TotalSuite\Foundation\Contracts\TrackingStorage;
use TotalRatingVendors\TotalSuite\Foundation\CronJobs\CheckLicense;
use TotalRatingVendors\TotalSuite\Foundation\CronJobs\TrackEnvironment;
use TotalRatingVendors\TotalSuite\Foundation\CronJobs\TrackEvents;
use TotalRatingVendors\TotalSuite\Foundation\Database\Connection;
use TotalRatingVendors\TotalSuite\Foundation\Database\Query;
use TotalRatingVendors\TotalSuite\Foundation\Filesystem\WordPressAdapter;
use TotalRatingVendors\TotalSuite\Foundation\Http\CookieJar;
use TotalRatingVendors\TotalSuite\Foundation\Http\Request;
use TotalRatingVendors\TotalSuite\Foundation\Http\ServerContext;
use TotalRatingVendors\TotalSuite\Foundation\Migration\Migrator;
use TotalRatingVendors\TotalSuite\Foundation\Validators\DateFormatRule;
use TotalRatingVendors\TotalSuite\Foundation\Validators\StringRule;
use TotalRatingVendors\TotalSuite\Foundation\View\Engine;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\ActionBus;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\ActionEmitter;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Admin\Page;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Admin\UninstallFeedback;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Database\WPConnection;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Modules\Manager;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Options;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Plugin;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Rest\ActionResolver;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Rest\Router;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Scheduler;

/**
 * Class CoreProvider
 *
 * @package TotalSuite\Foundation
 */
class CoreProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    /**
     * @var array
     */
    protected $provides = [
        ClassLoader::class,
        ExceptionHandlerContract::class,
        Connection::class,
        Filesystem::class,
        Options::class,
        Manager::class,
        Request::class,
        Emitter::class,
        CallableResolver::class,
        Router::class,
        Engine::class,
        Migrator::class,
        Validator::class,
        Scheduler::class,
        TrackingStorage::class,
        CookieJar::class,
    ];

    /**
     * @inheritDoc
     */
    public function register()
    {
        /**
         * @var Container $container
         */
        $container = $this->getContainer();

        /**
         * @var Environment $env
         */
        $env = $container->get(Environment::class);

        // Class loader
        $container->share(
            ClassLoader::class,
            static function () use ($env) {
                return $env['loader'];
            }
        );

        // Exception
        $container->share(
            ExceptionHandlerContract::class,
            static function () use ($env) {
                return new ExceptionHandler($env);
            }
        );

        // Filesystem
        $container->share(
            Filesystem::class,
            static function () use ($env) {
                $adapter = new WordPressAdapter($env->get('path.base'));
                $filesystem = new Filesystem($adapter, ['visibility' => AbstractAdapter::VISIBILITY_PUBLIC]);

                return $filesystem;
            }
        );

        // Plugins Options
        $container->share(Options::class)
                  ->addArgument($env['stores.optionsKey'])
                  ->addArgument($env->get('defaults.options', []));

        // Plugins License
        $container->share(License::class, function () {
            return new License('totalsuite_license', License::getDefault());
        });

        // Tracking Options
        $container->share(TrackingStorage::class, function () {
            return Options::instance()->withKey(Plugin::env('stores.trackingKey'), [
                'screens' => [],
                'features' => []
            ]);
        });

        // Module manager
        $container->share(Manager::class, Manager::class)
                  ->addArgument($container)
                  ->addArgument($env)
                  ->addArgument($container->get(Options::class)->withKey($env['stores.modulesKey']))
                  ->addArgument($container->get(ClassLoader::class));

        // Server Request
        $container->share(
            Request::class,
            static function () {
                return Request::createFromServer(ServerContext::create($_SERVER));
            }
        );

        // Cookie jar
        $container->share(CookieJar::class, static function () {
            return CookieJar::createFromServer();
        });

        // Event Emitter
        $container->share(
            Emitter::class,
            static function () {
                $emitter = new ActionEmitter();
                $emitter->addListener('*', new ActionBus(), ActionEmitter::P_LOW);
                return $emitter;
            }
        );

        // Route Callable Resolver
        $container->share(
            CallableResolver::class,
            static function () use ($container) {
                return new ActionResolver($container);
            }
        );

        // Router
        $container->share(Router::class)
                  ->addArgument($container->get(CallableResolver::class))
                  ->addArgument($env->get('namespaces.rest', $env->get('product.id')))
                  ->addArgument($container->get(ExceptionHandlerContract::class));

        // Scheduler
        $container->share(Scheduler::class, function () {
            $productId = Plugin::env('product.id');

            $scheduler = new Scheduler();
            $scheduler->addCronJob("{$productId}_weekly_environment", new TrackEnvironment());
            $scheduler->addCronJob("{$productId}_daily_activity", new TrackEvents());
            $scheduler->addCronJob('totalsuite_check_license', new CheckLicense());

            return $scheduler;
        });

        // Validator
        $container->share(
            Validator::class,
            static function () {
                $validator = new Validator();
                $validator->addValidator('string', new StringRule());
                $validator->addValidator('dateFormat', new DateFormatRule());

                return $validator;
            }
        );

        // Migrator
        $container->share(Migrator::class)->addArgument($container);

        // Uninstall feedback
        $container->share(UninstallFeedback::class, new UninstallFeedback());
    }

    /**
     * @inheritDoc
     */
    public function boot()
    {
        /**
         * @var Container $container
         */
        $container = $this->getContainer();

        /**
         * @var Environment $env
         */
        $env = $container->get(Environment::class);

        // Database
        $container->share(
            Connection::class,
            static function () use ($env) {
                return new WPConnection(DB_NAME, $env->get('db.prefix', 'wp_'));
            }
        );

        // Template Engine
        $container->share(
            Engine::class,
            static function () use ($env) {
                $engine = new Engine($env->get('path.base') . 'views');
                $engine->addFolder('marketing', dirname(__DIR__) . '/views/marketing');

                return $engine;
            }
        );

        // Initialize Model
        Query::setConnection($container->get(Connection::class));

        // Initialize View Engine
        Page::setEngine($container->get(Engine::class));
    }
}
