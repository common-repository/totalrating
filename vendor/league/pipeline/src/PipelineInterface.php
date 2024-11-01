<?php
declare(strict_types=1);

namespace TotalRatingVendors\League\Pipeline;
! defined( 'ABSPATH' ) && exit();


interface PipelineInterface extends StageInterface
{
    /**
     * Create a new pipeline with an appended stage.
     *
     * @return static
     */
    public function pipe(callable $operation): PipelineInterface;
}
