<?php
declare(strict_types=1);

namespace TotalRatingVendors\League\Pipeline;
! defined( 'ABSPATH' ) && exit();


interface StageInterface
{
    /**
     * Process the payload.
     *
     * @param mixed $payload
     *
     * @return mixed
     */
    public function __invoke($payload);
}
