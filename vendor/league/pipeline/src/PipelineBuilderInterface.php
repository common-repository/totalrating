<?php
declare(strict_types=1);

namespace TotalRatingVendors\League\Pipeline;
! defined( 'ABSPATH' ) && exit();


interface PipelineBuilderInterface
{
    /**
     * Add an stage.
     *
     * @return self
     */
    public function add(callable $stage): PipelineBuilderInterface;

    /**
     * Build a new Pipeline object.
     */
    public function build(ProcessorInterface $processor = null): PipelineInterface;
}
