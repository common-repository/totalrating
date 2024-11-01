<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Migration;
! defined( 'ABSPATH' ) && exit();


use Exception;
use TotalRatingVendors\League\Container\Container;
use ReflectionClass;
use ReflectionMethod;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\MigrationException;
use TotalRatingVendors\TotalSuite\Foundation\Filesystem;

/**
 * Class Migration
 * @package TotalRatingVendors\TotalSuite\Foundation\Migration
 */
abstract class Migration
{
    /**
     * @var string
     */
    protected $version;

    /**
     * @var string
     */
    protected $previousVersion;

    /**
     * @var string
     */
    protected $date;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $methods = [];

    /**
     * Migration constructor.
     *
     * @param Container $container
     * @param $path
     * @param $previousVersion
     */
    public function __construct(Container $container, $path, $previousVersion)
    {
        $this->container = $container;
        $this->path = $path;
        $this->previousVersion = $previousVersion;
        $this->initialize();
    }

    protected function initialize()
    {
        $reflection = new ReflectionClass($this);
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            if (stripos($method->name, 'apply') === 0) {
                $this->methods[] = $method->name;
            }
        }
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @throws MigrationException
     */
    final public function run()
    {
        $this->before();
        $errors = [];

        foreach ($this->methods as $method) {
            try {
                $this->$method();
            } catch (Exception $e) {
                $errors[$method] = sprintf('%s - %s:%d', $method, $e->getMessage(), $e->getCode());
            }
        }

        if (!empty($errors)) {
            throw new MigrationException(sprintf('Migration version %s failed', $this->version), $errors, 500);
        }

        $this->after();
    }

    protected function before()
    {
    }

    protected function after()
    {
    }
}