<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Database\Query;
! defined( 'ABSPATH' ) && exit();


/**
 * Class Expression
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Database\Query
 */
class Expression
{
    /**
     * @var
     */
    protected $value;

    /**
     * Expression constructor.
     *
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

}