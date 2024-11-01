<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Database\Query;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Database\Query\Concerns\Limit;
use TotalRatingVendors\TotalSuite\Foundation\Database\Query\Concerns\Order;
use TotalRatingVendors\TotalSuite\Foundation\Database\Query\Concerns\Where;

/**
 * Class Update
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Database\Query
 */
class Update extends BaseQuery
{
    use Where, Order, Limit;

    /**
     * @var array
     */
    protected $values = [];

    /**
     * @param string|array $column
     * @param mixed|null   $value
     *
     * @return Update
     */
    public function set($column, $value = null)
    {
        if (is_array($column)) {
            $this->values = array_merge($this->values, $column);
        } else {
            $this->values[$column] = $value;
        }

        return $this;
    }
}