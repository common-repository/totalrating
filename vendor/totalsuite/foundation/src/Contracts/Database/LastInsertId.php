<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Contracts\Database;
! defined( 'ABSPATH' ) && exit();


/**
 * Interface LastInsertId
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Contracts\Database
 */
interface LastInsertId
{
    /**
     * @return int
     */
    public function lastInsertId(): int;

    /**
     * @param string $query
     * @param array  $data
     *
     * @return int|null
     */
    public function insertGetId($query, array $data = []): int;
}