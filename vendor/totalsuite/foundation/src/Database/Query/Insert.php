<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Database\Query;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Contracts\Database\LastInsertId;

/**
 * Class Insert
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Database\Query
 */
class Insert extends BaseQuery
{
    /**
     * @var bool
     */
    protected $ignore = false;

    /**
     * @var bool
     */
    protected $updateOnDuplicateKey = false;

    /**
     * @var array
     */
    protected $values = [];

    /**
     * @param array $values
     *
     * @return $this
     */
    public function values(array $values)
    {
        $this->values = $values;
        return $this;
    }

    /**
     * @param bool $ignore
     *
     * @return $this
     */
    public function ignore($ignore = true)
    {
        $this->ignore = $ignore;
        return $this;
    }

    /**
     * @param bool $increment
     *
     * @return mixed
     */
    public function execute($increment = false)
    {
        list($query, $data) = $this->sql();

        if ($increment && $this->connection instanceof LastInsertId) {
            return $this->connection->insertGetId($query, $data);
        }

        return $this->connection->execute($query, $data);
    }
}