<?php

namespace TotalRatingVendors\Rakit\Validation\Rules;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\Rakit\Validation\Rule;

class Nullable extends Rule
{
    /**
     * Check the $value is valid
     *
     * @param mixed $value
     * @return bool
     */
    public function check($value): bool
    {
        return true;
    }
}
