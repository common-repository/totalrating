<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Contracts\Support;
! defined( 'ABSPATH' ) && exit();


use JsonSerializable;

/**
 * Interface Arrayable
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Contracts\Support
 */
interface Arrayable extends JsonSerializable
{
    /**
     * @return array
     */
    public function toArray(): array;
}