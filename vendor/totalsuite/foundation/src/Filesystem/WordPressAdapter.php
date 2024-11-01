<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Filesystem;
! defined( 'ABSPATH' ) && exit();


use DirectoryIterator;
use FilesystemIterator;
use finfo;
use TotalRatingVendors\League\Flysystem\Adapter\AbstractAdapter;
use TotalRatingVendors\League\Flysystem\Adapter\CanOverwriteFiles;
use TotalRatingVendors\League\Flysystem\AdapterInterface;
use TotalRatingVendors\League\Flysystem\Config;
use TotalRatingVendors\League\Flysystem\Util;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use WP_Filesystem_Base;

/**
 * Class WordPressAdapter
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Filesystem
 */
class WordPressAdapter extends AbstractAdapter implements AdapterInterface, CanOverwriteFiles
{
    /**
     * @var int
     */
    const SKIP_LINKS = 0001;

    /**
     * @var int
     */
    const DISALLOW_LINKS = 0002;

    /**
     * @var WP_Filesystem_Base
     */
    public $wpFilesystem;

    /**
     * @var string
     */
    protected $pathPrefix;

    /**
     * @var int
     */
    private $linkHandling;

    /**
     * WPAdapter constructor.
     *
     * @param string $pathPrefix
     *
     * @throws Exception
     */
    public function __construct($pathPrefix = DIRECTORY_SEPARATOR)
    {
        global $wp_filesystem;

        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';

        WP_Filesystem();

        $this->wpFilesystem = $wp_filesystem;
        // When Windows, avoid setting a path prefix
        if (stripos(PHP_OS, 'WIN') !== 0) {
            $this->setPathPrefix($this->ensureDirectory($pathPrefix));
        }
    }

    /**
     * @param $root
     *
     * @return string
     * @throws Exception
     */
    protected function ensureDirectory($root)
    {
        wp_mkdir_p($root);

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function write($path, $contents, Config $config)
    {
        $location = $this->applyPathPrefix($path);
        return $this->wpFilesystem->put_contents($location, $contents);
    }

    /**
     * @inheritDoc
     */
    public function update($path, $contents, Config $config)
    {
        $path = $this->applyPathPrefix($path);
        return $this->wpFilesystem->put_contents($path, $contents);
    }

    /**
     * @inheritDoc
     */
    public function updateStream($path, $resource, Config $config)
    {
        return $this->writeStream($path, $resource, $config);
    }

    /**
     * @inheritDoc
     */
    public function writeStream($path, $resource, Config $config)
    {
        $location = $this->applyPathPrefix($path);
        $this->ensureDirectory(dirname($location));
        $stream = fopen($location, 'w+b');

        if (!$stream || stream_copy_to_stream($resource, $stream) === false || !fclose($stream)) {
            return false;
        }

        return $this->wpFilesystem->chmod($location);
    }

    /**
     * @inheritDoc
     */
    public function rename($path, $newpath)
    {
        $location        = $this->applyPathPrefix($path);
        $destination     = $this->applyPathPrefix($newpath);
        $parentDirectory = $this->applyPathPrefix(Util::dirname($newpath));
        $this->ensureDirectory($parentDirectory);

        return rename($location, $destination);
    }

    /**
     * @inheritDoc
     */
    public function copy($path, $newpath)
    {
        $location    = $this->applyPathPrefix($path);
        $destination = $this->applyPathPrefix($newpath);
        $this->ensureDirectory(dirname($destination));

        return $this->wpFilesystem->copy($location, $destination);
    }

    /**
     * @inheritDoc
     */
    public function delete($path)
    {
        $location = $this->applyPathPrefix($path);
        return $this->wpFilesystem->delete($location, false, 'f');
    }

    /**
     * @inheritDoc
     */
    public function deleteDir($dirname)
    {
        $location = $this->applyPathPrefix($dirname);
        return $this->wpFilesystem->rmdir($location, true);
    }

    /**
     * @inheritDoc
     */
    public function createDir($dirname, Config $config)
    {
        $location = $this->applyPathPrefix($dirname);

        if ($this->wpFilesystem->mkdir($location)) {
            return ['path' => $dirname, 'type' => 'dir'];
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function has($path)
    {
        $location = $this->applyPathPrefix($path);
        return $this->wpFilesystem->exists($location);
    }

    /**
     * @inheritDoc
     */
    public function read($path)
    {
        $location = $this->applyPathPrefix($path);
        $contents = $this->wpFilesystem->get_contents($location);

        if ($contents === false) {
            return false;
        }

        return ['type' => 'file', 'path' => $path, 'contents' => $contents];
    }

    /**
     * @inheritDoc
     */
    public function readStream($path)
    {
        $location = $this->applyPathPrefix($path);
        $stream   = fopen($location, 'rb');

        return ['type' => 'file', 'path' => $path, 'stream' => $stream];
    }

    /**
     * @inheritDoc
     */
    public function listContents($directory = '', $recursive = false)
    {
        $result   = [];
        $location = $this->applyPathPrefix($directory);

        if (!is_dir($location)) {
            return [];
        }

        $iterator = $recursive ? $this->getRecursiveDirectoryIterator($location) : $this->getDirectoryIterator(
            $location
        );

        foreach ($iterator as $file) {
            $path = $this->getFilePath($file);

            if (preg_match('#(^|/|\\\\)\.{1,2}$#', $path)) {
                continue;
            }

            $result[] = $this->normalizeFileInfo($file);
        }

        unset($iterator);

        return array_filter($result);
    }

    /**
     * @param string $path
     * @param int    $mode
     *
     * @return RecursiveIteratorIterator
     */
    protected function getRecursiveDirectoryIterator($path, $mode = RecursiveIteratorIterator::SELF_FIRST)
    {
        return new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS), $mode
        );
    }

    /**
     * @param string $path
     *
     * @return DirectoryIterator
     */
    protected function getDirectoryIterator($path)
    {
        return new DirectoryIterator($path);
    }

    /**
     * Get the normalized path from a SplFileInfo object.
     *
     * @param SplFileInfo $file
     *
     * @return string
     */
    protected function getFilePath(SplFileInfo $file)
    {
        $location = $file->getPathname();
        $path     = $this->removePathPrefix($location);

        return trim(str_replace('\\', '/', $path), '/');
    }

    /**
     * Normalize the file info.
     *
     * @param SplFileInfo $file
     *
     * @return array|void
     *
     * @throws NotSupportedException
     */
    protected function normalizeFileInfo(SplFileInfo $file)
    {
        if (!$file->isLink()) {
            return $this->mapFileInfo($file);
        }

//        if ($this->linkHandling & self::DISALLOW_LINKS) {
//            throw NotSupportedException::forLink($file);
//        }
    }

    /**
     * @param SplFileInfo $file
     *
     * @return array
     */
    protected function mapFileInfo(SplFileInfo $file)
    {
        $normalized = [
            'type' => $file->getType(),
            'path' => $this->getFilePath($file),
        ];

        $normalized['timestamp'] = $file->getMTime();

        if ($normalized['type'] === 'file') {
            $normalized['size'] = $file->getSize();
        }

        return $normalized;
    }

    /**
     * @inheritDoc
     */
    public function getSize($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * @inheritDoc
     */
    public function getMetadata($path)
    {
        $location = $this->applyPathPrefix($path);
        clearstatcache(false, $location);
        $info = new SplFileInfo($location);

        return $this->normalizeFileInfo($info);
    }

    /**
     * @inheritDoc
     */
    public function getMimetype($path)
    {
        $location = $this->applyPathPrefix($path);
        $finfo    = new Finfo(FILEINFO_MIME_TYPE);
        $mimetype = $finfo->file($location);

        if (in_array($mimetype, ['application/octet-stream', 'inode/x-empty', 'application/x-empty'])) {
            $mimetype = Util\MimeType::detectByFilename($location);
        }

        return ['path' => $path, 'type' => 'file', 'mimetype' => $mimetype];
    }

    /**
     * @inheritDoc
     */
    public function getTimestamp($path)
    {
        $location = $this->applyPathPrefix($path);
        return $this->wpFilesystem->mtime($location);
    }

    /**
     * @inheritDoc
     */
    public function getVisibility($path)
    {
        $location   = $this->applyPathPrefix($path);
        $visibility = $this->wpFilesystem->getchmod($location);

        if (!$visibility) {
            return false;
        }

        return compact('path', 'visibility');
    }

    /**
     * @inheritDoc
     */
    public function setVisibility($path, $visibility)
    {
        $location = $this->applyPathPrefix($path);
        return $this->wpFilesystem->chmod($location, $visibility);
    }
}
