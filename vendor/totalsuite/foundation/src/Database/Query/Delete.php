<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Database\Query;
! defined( 'ABSPATH' ) && exit();



use TotalRatingVendors\TotalSuite\Foundation\Database\Query\Concerns\Limit;
use TotalRatingVendors\TotalSuite\Foundation\Database\Query\Concerns\Order;
use TotalRatingVendors\TotalSuite\Foundation\Database\Query\Concerns\Where;

/**
 * Class Delete
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Database\Query
 */
class Delete extends BaseQuery
{
    use Where, Order, Limit;
}