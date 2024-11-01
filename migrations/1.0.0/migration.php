<?php
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\League\Container\Container;
use TotalRatingVendors\TotalSuite\Foundation\Database\Connection;
use TotalRatingVendors\TotalSuite\Foundation\Filesystem;
use TotalRatingVendors\TotalSuite\Foundation\Migration\Migration;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Database\WPConnection;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Options;

/**
 * @class Anonymous migration class
 *
 * @param  Container  $container
 * @param           $path
 * @param           $previousVersion
 *
 * @return Migration
 */
return function (Container $container, $path, $previousVersion) {
    return new class($container, $path, $previousVersion) extends Migration {

        protected $version = '1.0.0';

        /**
         * @var Filesystem
         */
        protected $fs;

        /**
         * @var WPConnection
         */
        protected $db;

        /**
         * @var Options
         */
        protected $options;

        /**
         *  constructor.
         *
         * @param  Container  $container
         * @param           $path
         * @param           $previousVersion
         */
        public function __construct(Container $container, $path, $previousVersion)
        {
            parent::__construct($container, $path, $previousVersion);

            $this->db      = $this->container->get(Connection::class);
            $this->options = $this->container->get(Options::class);
        }


        public function applySchema()
        {
            $schema = glob(__DIR__.'/schema/*.sql');

            foreach ($schema as $file) {
                $sql = str_replace('{{prefix}}', $this->db->getTablePrefix(), file_get_contents($file));
                $this->db->raw(trim($sql));
            }
        }

        public function applyOptions()
        {
            $options = json_decode(file_get_contents($this->path.'/data/options.json'), true);
            $this->options->fill($options)->save();
        }
    };
};
