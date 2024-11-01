<?php

namespace TotalRatingVendors\TotalSuite\Foundation\WordPress\Tasks\Options;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Support\Collection;
use TotalRatingVendors\TotalSuite\Foundation\Task;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Options;

/**
 * Class ResetOptions
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\WordPress\Tasks\Options
 * @method static Collection invoke(Options $options)
 * @method static Collection invokeWithFallback($fallback, Options $options)
 */
class ResetOptions extends Task
{
    /**
     * @var Options
     */
    protected $options;

    /**
     * Reset constructor.
     *
     * @param Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }


    /**
     * @inheritDoc
     */
    protected function validate()
    {
        return true;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function execute()
    {
        Exception::throwUnless(
            $this->options->fill($this->options->getDefaults()->toArray())->save(),
            'Could not save options'
        );

        return $this->options->getBase();
    }
}
