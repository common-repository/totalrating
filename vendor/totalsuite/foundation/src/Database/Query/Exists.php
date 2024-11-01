<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Database\Query;
! defined( 'ABSPATH' ) && exit();



/**
 * Class Exists
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Database\Query
 */
class Exists extends BaseQuery
{
    /**
     * @var Select
     */
    protected $select;

    /**
     * @var bool
     */
    protected $exists = true;

    /**
     * @param callable $callback
     * @param bool     $exists
     *
     * @return Exists
     */
    public function exists(callable $callback, $exists = true)
    {
        $select = new Select($this->connection);
        $callback($select);

        $this->select = $select;
        $this->exists = $exists;

        return $this;
    }

    /**
     * @return bool
     */
    public function execute()
    {
        return (bool)parent::execute();
    }


}