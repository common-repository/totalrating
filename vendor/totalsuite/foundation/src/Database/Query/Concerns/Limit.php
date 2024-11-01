<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Database\Query\Concerns;
! defined( 'ABSPATH' ) && exit();



/**
 * Trait Limit
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Database\Query\Concerns
 */
trait Limit
{
    /**
     * @var int
     */
    protected $offset;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @param int $perPage
     * @param int $current
     *
     * @return static
     */
    public function page(int $perPage = 10, int $current = 1)
    {
        $current = ($current < 1) ? 1 : $current;
        $perPage = ($perPage < 1) ? 1 : $perPage;

        $this->limit($perPage, ($current - 1) * $perPage);

        return $this;
    }

    /**
     * @param int      $limit
     * @param int|null $offset
     *
     * @return static
     */
    public function limit(int $limit, $offset = null)
    {
        $this->limit  = $limit;
        $this->offset = $offset !== null ? (int)$offset : null;

        return $this;
    }
}