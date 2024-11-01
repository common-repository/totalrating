<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Contracts;
! defined( 'ABSPATH' ) && exit();


/**
 * Interface Action
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Contracts
 */
interface Action
{
    /**
     * @return bool
     */
    public function authorize(): bool;

    /**
     * @return array
     */
    public function getParams(): array;
}