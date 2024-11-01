<?php

namespace TotalRatingVendors\TotalSuite\Foundation\WordPress;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\League\Container\Container;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Modules\Definition;

/**
 * Class Module
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\WordPress
 */
abstract class Module
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var Definition
     */
    protected $definition;

    /**
     * Module constructor.
     *
     * @param Definition $definition
     * @param Container  $container
     */
    public function __construct(Definition $definition, Container $container)
    {
        $this->definition = $definition;
        $this->container  = $container;

        $this->path = $definition->get('path');
        $this->url  = $definition->get('baseUrl');
    }

    public static function onInstall()
    {
    }

    public static function onUninstall()
    {
    }

    /**
     * @param string $relative
     *
     * @return string
     */
    public function getPath($relative = ''): string
    {
        return realpath(wp_normalize_path($this->path . DIRECTORY_SEPARATOR . ltrim($relative, '/\\')));
    }

    /**
     * @param string $relative
     *
     * @return string
     */
    public function getUrl($relative = ''): string
    {
        return $this->url . rtrim($relative, '/');
    }

    /**
     * @return Definition
     */
    public function getDefinition(): Definition
    {
        return $this->definition;
    }

    public function onActivate()
    {
    }

    public function onDeactivate()
    {
    }
}