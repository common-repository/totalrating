<?php

namespace TotalRatingVendors\League\Flysystem\Plugin;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\League\Flysystem\FilesystemInterface;
use TotalRatingVendors\League\Flysystem\PluginInterface;

abstract class AbstractPlugin implements PluginInterface
{
    /**
     * @var FilesystemInterface
     */
    protected $filesystem;

    /**
     * Set the Filesystem object.
     *
     * @param FilesystemInterface $filesystem
     */
    public function setFilesystem(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }
}
