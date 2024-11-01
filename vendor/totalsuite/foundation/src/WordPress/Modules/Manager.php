<?php

namespace TotalRatingVendors\TotalSuite\Foundation\WordPress\Modules;
! defined( 'ABSPATH' ) && exit();


use Composer\Autoload\ClassLoader;
use TotalRatingVendors\League\Container\Container;
use Throwable;
use TotalRatingVendors\TotalSuite\Foundation\Environment;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\ModuleException;
use TotalRatingVendors\TotalSuite\Foundation\Filesystem;
use TotalRatingVendors\TotalSuite\Foundation\Helpers\Concerns\ResolveFromContainer;
use TotalRatingVendors\TotalSuite\Foundation\Support\Collection;
use TotalRatingVendors\TotalSuite\Foundation\Support\Strings;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Module;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Options;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Plugin;
use WP_Error;
use ZipArchive;

/**
 * Class Manager
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\WordPress\Module
 */

/**
 * Class Manager
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\WordPress\Modules
 */
class Manager
{
    use ResolveFromContainer;

    /**
     * @var Collection<Definition>|Definition[]
     */
    protected $definitions;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Environment
     */
    protected $environment;

    /**
     * @var Options
     */
    protected $activatedModules;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var ClassLoader
     */
    protected $loader;

    /**
     * Manager constructor.
     *
     * @param Container $container
     * @param Environment $env
     * @param Filesystem $fs
     * @param Options $options
     *
     * @param ClassLoader $loader
     *
     * @throws \Exception
     */
    public function __construct(
        Container   $container,
        Environment $env,
        Options     $options,
        ClassLoader $loader
    )
    {
        $this->container = $container;
        $this->environment = $env;
        $this->activatedModules = $options;
        $this->loader = $loader;

        // Setup user modules auto-loading
        $userExtensionsPath = wp_normalize_path($this->environment->get('path.userModules') . '/extensions');
        $userExtensionsNamespace = $this->environment->get('namespaces.extension') . '\\';
        $this->loader->addPsr4($userExtensionsNamespace, [$userExtensionsPath]);

        $userTemplatesPath = wp_normalize_path($this->environment->get('path.userModules') . '/templates');
        $userTemplatesNamespace = $this->environment->get('namespaces.template') . '\\';
        $this->loader->addPsr4($userTemplatesNamespace, [$userTemplatesPath]);

        $this->fetchLocal();
    }

    /**
     * @return Collection
     * @throws \Exception
     */
    public function fetchLocal(): Collection
    {
        $pluginModules = glob($this->environment->get('path.modules') . '/**/**/module.json');
        $userModules = glob($this->environment->get('path.userModules') . '/**/**/module.json');

        $pluginModulesUrl = $this->environment->get('url.modules.base');
        $userModulesUrl = $this->environment->get('url.userModules.base');

        $localDefinitions = $this->createDefinitionsFromStorage($this->environment->get('path.modules'), $pluginModulesUrl, $pluginModules);
        $userDefinitions = $this->createDefinitionsFromStorage($this->environment->get('path.userModules'), $userModulesUrl, $userModules);

        $this->definitions = Collection::create(array_merge($localDefinitions, $userDefinitions));

        return $this->definitions;
    }

    /**
     * @param Filesystem $storage
     * @param $baseUrl
     * @param $files
     *
     * @return array
     * @throws \Exception
     */
    public function createDefinitionsFromStorage($pathPrefix, $baseUrl, $files)
    {
        $definitions = [];

        foreach ($files as $file) {
            $filename = wp_normalize_path(realpath($file));
            $module = json_decode(file_get_contents($filename), true);

            $relativePath = str_replace($pathPrefix, '', dirname($file));
            $url = $baseUrl . '/' . $relativePath;

            $path = wp_normalize_path(dirname($file));


            $module = $this->createDefinition($module, $path, $url . '/');
            $definitions[$module['id']] = $module;
        }

        return $definitions;
    }

    /**
     * @param array $module
     * @param string $path
     *
     * @param null $url
     *
     * @return Definition
     * @throws \Exception
     */
    protected function createDefinition(array $module, $path = null, $url = null): Definition
    {
        $module['id'] = $module['id'] ?: Strings::uid();
        $module['path'] = $path;
        $module['baseUrl'] = $url;

        if (!empty($module['images']['icon'])) {
            $module['images']['icon'] = str_replace('./', $module['baseUrl'], $module['images']['icon']);
        }

        if (!empty($module['images']['cover'])) {
            $module['images']['cover'] = str_replace('./', $module['baseUrl'], $module['images']['cover']);
        }

        $module['class'] = implode(
            '\\',
            [
                $this->environment->get('namespaces.' . $module['type']),
                ucfirst(basename($module['path'], DIRECTORY_SEPARATOR)),
                'Module',
            ]
        );

        $module['activated'] = empty($module['builtIn']) ? $this->activatedModules->has($module['id']) : true;
        $module['installed'] = true;

        return new Definition($module);
    }

    /**
     * @return Collection
     */
    public function getDefinitions(): Collection
    {
        return $this->definitions;
    }

    /**
     * @param $id
     *
     * @return Definition|null
     */
    public function getDefinition($id)
    {
        return $this->definitions->get($id);
    }

    /**
     * @param string $module
     *
     * @return Definition
     * @throws Exception
     */
    public function installFromFile($module): Definition
    {
        $definition = $this->extractModule($module);

        forward_static_call([$definition['class'], 'onInstall']);

        return $definition;
    }

    /**
     * @param string $archive
     *
     * @return Definition
     * @throws Exception
     */
    protected function extractModule($archive): Definition
    {
        $zip = new ZipArchive();

        $zip->open($archive);

        ModuleException::throwUnless($zip->open($archive), $zip->getStatusString());

        $moduleData = $zip->getFromName($zip->getNameIndex(0) . 'module.json');
        ModuleException::throwUnless($moduleData, 'Unable to find module.json definition');

        $moduleDirName = $zip->getNameIndex(0);
        $moduleData = json_decode($moduleData, true);
        $modulesBasePath = $this->environment->get('path.userModules');
        $moduleTypeDir = $moduleData['type'] === Definition::TYPE_EXTENSION ? 'extensions' : 'templates';
        $modulesPath = wp_normalize_path($modulesBasePath . DIRECTORY_SEPARATOR . $moduleTypeDir . DIRECTORY_SEPARATOR);
        $modulePath = wp_normalize_path($modulesPath . $moduleDirName);
        $modulesBaseUrl = $this->environment->get('url.userModules.base');
        $moduleUrl = $modulesBaseUrl . '/' . $moduleTypeDir . '/' . $moduleDirName;

        $definition = $this->createDefinition($moduleData, $modulePath, $moduleUrl);

        $definition->validate();

        ModuleException::throwUnless($zip->extractTo($modulesPath), $zip->getStatusString());

        return $definition;
    }

    /**
     * @param string $module
     *
     * @throws Exception
     */
    public function installFromStore($module)
    {
        $store = $this->fetchStore();

        if ($definition = $store->get($module, false)) {
            $module = download_url($definition['downloadUrl']);

            if ($module instanceof WP_Error) {
                ModuleException::throw($module->get_error_message());
            }
        }

        $definition = $this->extractModule($module);
        forward_static_call([$definition['class'], 'onInstall']);
    }

    /**
     * @param array $data
     *
     * @return Collection
     * @throws \Exception
     */
    public function fetchStore(array $data = []): Collection
    {
        // Retrieve from cache first
        $cacheKey = md5($this->environment['url.modules.store']);
        if ($cached = get_transient($cacheKey)):
            return Collection::create($cached);
        endif;

        // Fetch
        $url = add_query_arg($data, $this->environment['url.modules.store']);
        $request = wp_remote_get($url);

        // Decode response
        $response = json_decode(wp_remote_retrieve_body($request), true) ?: [];
        $modules = [];

        if (!empty($response['data'])):
            $modules = $response['data'];

            // Cache
            set_transient($cacheKey, $modules, DAY_IN_SECONDS);
        endif;

        // Parse
        $definitions = [];

        foreach ($modules as $module) {
            $definition = new Definition($module);
            $definitions[$module['id'] ?: Strings::uid()] = $definition;
        }

        return Collection::create($definitions);
    }

    /**
     * @param $id
     *
     * @return bool
     */
    public function uninstall($id): bool
    {
        try {
            $definition = $this->definitions->get($id);
            Exception::throwUnless($definition, 'Module not found');

            if ($definition->isBuiltIn()) {
                return true;
            }

            forward_static_call([$definition['class'], 'onUninstall']);

            $baseDir = dirname($definition['path']);
            $moduleDir = basename($definition['path']);

            /**
             * @var $wp_filesystem \WP_Filesystem_Direct
             */
            global $wp_filesystem;

            require_once ABSPATH . 'wp-admin/includes/file.php';
            WP_Filesystem();

            $wp_filesystem->rmdir(wp_normalize_path($baseDir . '/' . $moduleDir), true);
        } catch (Throwable $e) {
            wp_send_json_error($e->getMessage());
        }

        return false;
    }

    /**
     * @param $id
     *
     * @return bool
     * @throws ModuleException|Exception
     */
    public function deactivate($id): bool
    {
        if (!$this->activatedModules->has($id)) {
            return false;
        }

        /**
         * @var Definition $definition
         */
        $definition = $this->definitions->get($id);

        ModuleException::throwUnless($definition instanceof Definition, sprintf('module %s definition not found', $id));

        if ($definition->isExtension()) {
            $this->getModuleInstance($id)
                 ->onDeactivate();
        } elseif ($definition->isTemplate()) {
            $this->loadTemplate($id)
                 ->onDeactivate();
        }

        return $this->activatedModules->remove($id)->save();
    }

    /**
     * @param $id
     *
     * @return Module
     */
    protected function getModuleInstance($id): Module
    {
        $definition = $this->definitions->get($id);

        return $this->container->get($definition['class']);
    }

    /**
     * @param Definition $definition
     *
     * @return Module
     * @throws ModuleException
     */
    protected function loadModule(Definition $definition): Module
    {
        $definition->validate();
        if (version_compare($definition['requires'], $this->environment->get('version'), '>')) {
            throw new ModuleException(
                sprintf('Module %s requires product version %s or higher', $definition['id'], $definition['requires'])
            );
        }

        if (!class_exists($definition['class'])) {
            throw new ModuleException(
                sprintf('Module %s class could not be found: %s', $definition['id'], $definition['class'])
            );
        }

        if (!in_array(Module::class, class_parents($definition['class']), true)) {
            throw new ModuleException(sprintf('Module %s class must extend %s', $definition['id'], Module::class));
        }

        try {
            $instance = new $definition['class']($definition, $this->container);
        } catch (\Exception $exception) {
            throw new ModuleException(
                sprintf('Module %s could not be loaded: %s', $definition['id'], $exception->getMessage())
            );
        }

        $definition->set('loaded', true);

        return $instance;
    }

    /**
     * @param $id
     *
     * @return bool
     * @throws ModuleException
     */
    public function activate($id): bool
    {
        $definition = $this->definitions->get($id);

        if ($definition instanceof Definition) {
            $this->loadModule($definition)
                 ->onActivate();

            return $this->activatedModules->set($id, true)->save();
        }

        throw new ModuleException(sprintf('Unable de activate module %s, module not found', $id));
    }

    /**
     * @param $id
     */
    public function update($id)
    {
    }

    /**
     * Load and initialize extensions
     *
     * @throws ModuleException
     */
    public function loadExtensions(): Manager
    {
        $extensions = $this->fetchLocal()
                           ->where(['activated' => true, 'type' => Definition::TYPE_EXTENSION]);
        /**
         * @var Definition $extension
         */
        foreach ($extensions as $extension) {
            try {
                $this->container->share($extension->get('class'), $this->loadModule($extension));
            } catch (ModuleException $exception) {
                // Deactivate the module when 'autofix' query param is present
                if (Plugin::request('autofix')) {
                    $this->activatedModules->remove($extension->get('id'))->save();
                } elseif ($this->environment->isDebug()) {
                    // For debugging purpose
                    throw $exception;
                }
            }
        }

        return $this;
    }

    /**
     * Load and initialize template
     *
     * @param        $templateId
     *
     * @param string $fallback
     *
     * @return Template
     * @throws ModuleException
     */
    public function loadTemplate($templateId, $fallback = 'default-template')
    {
        if (!$this->container->has($templateId)) {
            $definition = $this->definitions->get($templateId, false);

            if (!$definition instanceof Definition) {
                $templateId = $fallback;
                $definition = $this->definitions->get($templateId, false);
            }

            $this->container->add($templateId, $this->loadModule($definition));
        }

        return $this->container->get($templateId);
    }

    /**
     * @return Collection
     * @throws \Exception
     */
    public function fetch(): Collection
    {
        $localModules = $this->fetchLocal();
        $storeModules = $this->fetchStore();

        /**
         * @var Definition $module
         */
        foreach ($localModules as $id => $module) {
            if (isset($storeModules[$id])) {
                $storeModule = $storeModules[$id];
                $storeModule['path'] = $module['path'];
                $storeModule['class'] = $module['class'];
                $storeModule['version'] = $module['version'];
                $storeModule['baseUrl'] = $module['baseUrl'];
                $storeModule['activated'] = $module['activated'];
                $storeModule['installed'] = true;

                $localModules[$id] = $storeModule;
                unset($storeModules[$id]);
            }
        }

        return $localModules->merge($storeModules->all());
    }
}
