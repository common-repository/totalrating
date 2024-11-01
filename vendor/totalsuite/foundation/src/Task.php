<?php

namespace TotalRatingVendors\TotalSuite\Foundation;
! defined( 'ABSPATH' ) && exit();


use Exception;
use Throwable;

/**
 * Class Task
 *
 * @package TotalSuite\Foundation
 */
abstract class Task
{
    /**
     * @return mixed
     */
    public function __invoke()
    {
        return $this->run();
    }

    /**
     * @return mixed
     */
    final public function run()
    {
        $this->validate();
        return $this->execute();
    }

    /**
     * @return mixed|void
     */
    abstract protected function validate();

    /**
     * @return mixed
     */
    abstract protected function execute();

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        try {
            $this->validate();
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Shorthand to run a task.
     *
     * @param mixed ...$arguments
     * @return mixed
     * @throws \TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception
     */
    public static function invoke(...$arguments)
    {
        return (new static(...$arguments))->run();
    }

    /**
     * Shorthand to run a task and return a fallback when an exception is thrown.
     *
     * @param bool $fallback
     * @param mixed ...$arguments
     * @return mixed
     */
    public static function invokeWithFallback($fallback = false, ...$arguments)
    {
        try {
            return static::invoke(...$arguments);
        } catch (Throwable $e) {
            return $fallback;
        }
    }
}