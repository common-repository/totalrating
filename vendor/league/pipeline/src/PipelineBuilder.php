<?php
declare(strict_types=1);

namespace TotalRatingVendors\League\Pipeline;
! defined( 'ABSPATH' ) && exit();


class PipelineBuilder implements PipelineBuilderInterface
{
    /**
     * @var callable[]
     */
    private $stages = [];

    /**
     * @return self
     */
    public function add(callable $stage): PipelineBuilderInterface
    {
        $this->stages[] = $stage;

        return $this;
    }

    public function build(ProcessorInterface $processor = null): PipelineInterface
    {
        return new Pipeline($processor, ...$this->stages);
    }
}
