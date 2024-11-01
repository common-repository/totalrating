<?php

namespace TotalRatingVendors\TotalSuite\Foundation\WordPress;
! defined( 'ABSPATH' ) && exit();


use Composer\Autoload\ClassLoader;
use TotalRatingVendors\League\Container\Container;
use TotalRatingVendors\League\Container\ServiceProvider\ServiceProviderInterface;
use TotalRatingVendors\TotalSuite\Foundation\Contracts\ExceptionHandler;
use TotalRatingVendors\TotalSuite\Foundation\CoreProvider;
use TotalRatingVendors\TotalSuite\Foundation\Emitter;
use TotalRatingVendors\TotalSuite\Foundation\Environment;
use TotalRatingVendors\TotalSuite\Foundation\Event;
use TotalRatingVendors\TotalSuite\Foundation\Events\Modules\OnActivateModule;
use TotalRatingVendors\TotalSuite\Foundation\Events\Modules\OnDeactivateModule;
use TotalRatingVendors\TotalSuite\Foundation\Events\Modules\OnInstallModule;
use TotalRatingVendors\TotalSuite\Foundation\Events\Modules\OnUninstallModule;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Handlers\Tracking\HandleModuleEvents;
use TotalRatingVendors\TotalSuite\Foundation\Http\Request;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Modules\Manager;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Rest\Router;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Tasks\Activation\SetupUpdateChecks;

/**
 * Class Plugin
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\WordPress
 */
abstract class Plugin
{
    /**
     * @var Plugin
     */
    protected static $instance;

    /**
     * @var Container
     */
    protected $container;

    /**
     * Plugins constructor.
     *
     * @param array $environment
     */
    protected function __construct(array $environment = [])
    {
        // Setup environment
        $env = new Environment($environment);

        // Prepare container
        $this->container = new Container();
        $this->container->share(Environment::class, $env)->addTag('env');

        // Check debug mode
        /** @noinspection ClassConstantCanBeUsedInspection */
        if ($env->isDebug() && class_exists('\Whoops\Run')) {
            /** @noinspection PhpFullyQualifiedNameUsageInspection */
            $whoops = new \Whoops\Run();
            /** @noinspection PhpFullyQualifiedNameUsageInspection */
            $whoops->prependHandler(new \Whoops\Handler\PrettyPageHandler());
            $whoops->register();
        }

        // Core Service Provider
        $this->container->addServiceProvider(new CoreProvider());

        // Plugins Custom Providers
        $this->container->addServiceProvider($this->getServiceProvider());

        // Activation
        register_activation_hook($env->get('path.base') . 'plugin.php', [$this, 'onActivation']);

        // Deactivation
        register_deactivation_hook($env->get('path.base') . 'plugin.php', [$this, 'onDeactivation']);

        // Uninstall
        register_uninstall_hook($env->get('path.base') . 'plugin.php', [static::class, 'onUninstall']);

        // Register short codes
        add_action('init', [$this, 'registerShortCodes']);

        // Register assets
        add_action('init', [$this, 'registerAssets']);

        // register widgets
        add_action('widgets_init', [$this, 'registerWidgets']);

        // load translations
        add_action('init', [$this, 'loadTextDomain']);
    }

    /**
     * Bootstrap the plugin.
     */
    protected function bootstrap()
    {
        try {
            // Generate product instance UUID
            static::uid();
            static::firstUsage();

            /**
             * @var Manager $manager
             */
            $manager = $this->container->get(Manager::class);

            // Load extensions
            $manager->loadExtensions();

            // Plugins routes
            $this->registerRoutes($this->container->get(Router::class));

            // Register Events
            $handleModuleEvents = new HandleModuleEvents();

            OnActivateModule::listen($handleModuleEvents);
            OnDeactivateModule::listen($handleModuleEvents);
            OnInstallModule::listen($handleModuleEvents);
            OnUninstallModule::listen($handleModuleEvents);

            // Register cron jobs
            Scheduler::instance()->register();

            // Check updates
            SetupUpdateChecks::invoke();

            // Run the plugin business logic
            $this->run();
        } catch (Exception $e) {
            $this->container(ExceptionHandler::class)->handle($e);
        }
    }

    /**
     * Get an item from the environment.
     * When the key is null, the Environment instance
     * is returned.
     *
     * @param string|null $key
     * @param mixed|null $default
     *
     * @return Environment|mixed
     */
    public static function env($key = null, $default = null)
    {
        /**
         * @var Environment $env
         */
        $env = static::instance()->get(Environment::class);

        if ($key === null) {
            return $env;
        }

        return $env->get($key, $default);
    }

    /**
     * Get an item from the options store.
     * When the key is null, the Options instance
     * is returned.
     *
     * @param string $key
     * @param mixed|null $default
     *
     * @return Options|mixed
     */
    public static function options($key = null, $default = null)
    {
        /**
         * @var Options $options
         */
        $options = static::instance()->container(Options::class);

        return $key === null ? $options : $options->get($key, $default);
    }

    /**
     * Get an item from the HTTP request.
     * When the key is null, the Request instance
     * is returned.
     *
     * @param string|null $key
     * @param mixed|null $default
     *
     * @return Request|mixed
     */
    public static function request($key = null, $default = null)
    {
        /**
         * @var Request $request
         */
        $request = static::instance()->container(Request::class);
        return $key === null ? $request : $request->getParam($key, $default);
    }

    /**
     * Get the class loader.
     *
     * @return ClassLoader
     * @noinspection PhpUndefinedClassInspection
     */
    public static function loader()
    {
        return static::instance()->container(ClassLoader::class);
    }

    /**
     * Get the container.
     *
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Get an instance from the container.
     * When the key is null, the Container instance
     * is returned.
     *
     * @param string|null $key
     *
     * @return Container|mixed
     */
    public function container($key = null)
    {
        if ($key === null) {
            return $this->container;
        }

        return $this->container->get($key);
    }

    /**
     * Static version of $this->container().
     *
     * @param string|null $key
     *
     * @return mixed
     */
    public static function get($key = null)
    {
        return static::instance()->container($key);
    }

    /**
     * Get the main instance of this plugin.
     *
     * @param array $environment
     *
     * @return static
     */
    public static function instance(array $environment = []): self
    {
        if (static::$instance === null) {
            static::$instance = new static($environment);
            static::$instance->bootstrap();
        }

        return static::$instance;
    }

    /**
     * Emit an event.
     *
     * @param Event|string $event
     *
     * @return Event
     */
    public static function emit($event): Event
    {
        return static::$instance->container->get(Emitter::class)->emit($event);
    }

    /**
     * Listen to an event.
     *
     * @param Event|string $event
     * @param string|array|callable|object $listener
     * @param int $priority
     *
     * @return Emitter
     */
    public static function listen($event, $listener, $priority = Emitter::P_NORMAL): Emitter
    {
        return static::$instance->container->get(Emitter::class)->addListener($event, $listener, $priority);
    }

    /**
     * Remove event listener(s).
     * When $listener is provided, only that listener will be removed.
     *
     * @param Event|string $event
     * @param string|array|callable|object $listener
     * @return Emitter
     */
    public static function silent($event, $listener = null): Emitter
    {
        if ($listener) {
            return static::$instance->container->get(Emitter::class)->removeListener($event, $listener);
        }

        return static::$instance->container->get(Emitter::class)->removeAllListeners($event);
    }

    /**
     * @return array
     */
    public function objectsCount()
    {
        return [];
    }

    /**
     * @return string
     */
    public static function uid()
    {
        $uid = Options::instance()->get('instanceUid', false);

        if (!$uid) {
            $uid = wp_generate_uuid4();
            Options::instance()->set('instanceUid', $uid)->save();
        }

        return $uid;
    }

    /**
     * @return string
     */
    public static function firstUsage()
    {
        $date = Options::instance()->get('firstUsage', false);

        if (!$date) {
            $date = date(DATE_ATOM);
            Options::instance()->set('firstUsage', $date)->save();
        }

        return $date;
    }

    /**
     * Load text domain and custom expressions.
     */
    public function loadTextDomain()
    {
        $expressions = static::options('expressions', []);

        if (!static::env()->isAdmin() || static::env()->isAjax()) {
            add_filter(
                'gettext_' . static::env('textdomain'),
                function ($translation, $text, $domain) use (&$expressions) {
                    if (!empty($expressions[$text]['translations'][0])) {
                        return $expressions[$text]['translations'][0];
                    }

                    return $translation;
                },
                10,
                3
            );

            add_filter(
                'ngettext_' . static::env('textdomain'),
                function ($translation, $single, $plural, $number, $domain) use (&$expressions) {
                    if (!empty($expressions[$single]['translations'][$number < 2 ? 0 : 1])) {
                        return $expressions[$single]['translations'][$number < 2 ? 0 : 1];
                    }

                    return $translation;
                },
                10,
                5
            );
        }

        $locale = is_admin() && !static::env()->isAjax() ? get_user_locale() : get_locale();
        $localeFallback = substr($locale, 0, 2);
        $textDomain = static::env('textdomain');
        $moFileFallback = "{$textDomain}-{$localeFallback}.mo";
        $path = static::env('path.languages');

        $loaded = load_plugin_textdomain($textDomain, false, '/languages');

        if (!$loaded):
            load_textdomain($textDomain, "{$path}/{$moFileFallback}");
        endif;
    }

    /**
     * Run the plugin logic.
     *
     * @return void
     */
    abstract public function run();

    /**
     * Get plugin's service provider.
     *
     * @return ServiceProviderInterface
     */
    abstract public function getServiceProvider(): ServiceProviderInterface;

    /**
     * Register REST API routes.
     *
     * @param Router $router
     *
     * @return void
     */
    abstract public function registerRoutes(Router $router);

    /**
     * Register plugin's shortcodes.
     *
     * @return void
     */
    abstract public function registerShortCodes();

    /**
     * Register plugin's widgets.
     *
     * @return void
     */
    abstract public function registerWidgets();

    /**
     * Register plugin's assets.
     *
     * @return void
     */
    abstract public function registerAssets();

    /**
     * Called when activating the plugin.
     *
     * @return void
     */
    abstract public function onActivation();

    /**
     * Called when deactivating the plugin.
     *
     * @return void
     */
    abstract public function onDeactivation();

    /**
     * Called when uninstalling the plugin.
     *
     * @return void
     */
    abstract public static function onUninstall();
}
