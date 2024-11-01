<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Filesystem\Plugins;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\League\Flysystem\FileNotFoundException;
use TotalRatingVendors\League\Flysystem\FilesystemInterface;
use TotalRatingVendors\League\Flysystem\PluginInterface;

/**
 * Class JsonPlugin
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Filesystem\Plugins
 */
class JsonPlugin implements PluginInterface
{
    /**
     * @var FilesystemInterface
     */
    protected $filesystem;

    /**
     * @param      $path
     * @param null $content
     *
     * @return bool|mixed
     * @throws FileNotFoundException
     */
    public function handle($path, $content = null)
    {
        $prefix = $this->filesystem->getAdapter()->getPathPrefix();

        $path = str_replace($prefix, '', $path);

        if ($content === null) {
            return json_decode($this->filesystem->read($path), true);
        }

        return $this->filesystem->put($path, json_encode($content));
    }

    /**
     * @inheritDoc
     */
    public function getMethod(): string
    {
        return 'json';
    }

    /**
     * @inheritDoc
     */
    public function setFilesystem(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }
}
