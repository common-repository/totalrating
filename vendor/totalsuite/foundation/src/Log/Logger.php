<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Log;
! defined( 'ABSPATH' ) && exit();



use TotalRatingVendors\Psr\Log\LoggerInterface;

/**
 * Class Logger
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Log
 */
class Logger implements LoggerInterface
{
    /**
     * @var
     */
    protected $adapter;

    /**
     * @inheritDoc
     */
    public function emergency($message, array $context = [])
    {
        // TODO: Implement emergency() method.
    }

    /**
     * @inheritDoc
     */
    public function alert($message, array $context = [])
    {
        // TODO: Implement alert() method.
    }

    /**
     * @inheritDoc
     */
    public function critical($message, array $context = [])
    {
        // TODO: Implement critical() method.
    }

    /**
     * @inheritDoc
     */
    public function error($message, array $context = [])
    {
        // TODO: Implement error() method.
    }

    /**
     * @inheritDoc
     */
    public function warning($message, array $context = [])
    {
        // TODO: Implement warning() method.
    }

    /**
     * @inheritDoc
     */
    public function notice($message, array $context = [])
    {
        // TODO: Implement notice() method.
    }

    /**
     * @inheritDoc
     */
    public function info($message, array $context = [])
    {
        // TODO: Implement info() method.
    }

    /**
     * @inheritDoc
     */
    public function debug($message, array $context = [])
    {
        // TODO: Implement debug() method.
    }

    /**
     * @inheritDoc
     */
    public function log($level, $message, array $context = [])
    {
        // TODO: Implement log() method.
    }
}