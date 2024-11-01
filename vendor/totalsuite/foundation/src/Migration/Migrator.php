<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Migration;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\League\Container\Container;
use TotalRatingVendors\TotalSuite\Foundation\Database\Connection;
use TotalRatingVendors\TotalSuite\Foundation\Environment;
use TotalRatingVendors\TotalSuite\Foundation\Filesystem;
use TotalRatingVendors\TotalSuite\Foundation\Helpers\Concerns\ResolveFromContainer;
use TotalRatingVendors\TotalSuite\Foundation\Migration\Concerns\MigrationQueue;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Database\WPConnection;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Options;

/**
 * Class Migrator
 * @package TotalRatingVendors\TotalSuite\Foundation\Migration
 */
class Migrator
{
    use MigrationQueue, ResolveFromContainer;

    /**
     * @var Environment
     */
    protected $env;

    /**
     * @var WPConnection
     */
    protected $connection;

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var Options
     */
    protected $options;

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
    protected $currentVersion;

    /**
     * Migrator constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->env = $container->get(Environment::class);
        $this->connection = $container->get(Connection::class);
        $this->path = $this->env->get('path.migrations');
        $this->options = $container->get(Options::class)->withKey($this->env->get('stores.versionKey'));
        $this->container = $container;

        $this->currentVersion = $this->options->get('version');

        $this->fetchMigrations();
    }

    public function execute()
    {
        if (empty($this->migrations)) {
            return;
        }

        $currentVersion = null;
        $previousVersion = null;

        foreach ($this->migrations as $currentVersion => $migration) {
            $instance = $this->loadMigration($migration, $previousVersion);
            $instance->run();
            $previousVersion = $currentVersion;
        }

        $this->options->fill([
            'version' => $currentVersion,
            'date' => date('Y-m-d H:i:s')
        ])->save();

        $this->currentVersion = $currentVersion;
    }

}