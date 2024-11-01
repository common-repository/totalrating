<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Database\Query;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Database\Query\Concerns\Limit;
use TotalRatingVendors\TotalSuite\Foundation\Database\Query\Concerns\Order;
use TotalRatingVendors\TotalSuite\Foundation\Database\Query\Concerns\Where;

/**
 * Class Select
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Database\Query
 */
class Select extends BaseQuery
{
    const ORDER_DESC = 'DESC';
    const ORDER_ASC = 'ASC';

    use Where, Order, Limit;

    /**
     * @var bool
     */
    protected $distinct = false;

    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @var array
     */
    protected $joins = [];

    /**
     * @var array
     */
    protected $groups = [];

    /**
     * @var array
     */
    protected $having = [];

    /**
     * @param array $columns
     * @param bool  $merge
     *
     * @return $this
     */
    public function columns(array $columns, $merge = true)
    {
        if ($merge === false) {
            $this->columns = [];
        }

        foreach ($columns as $alias => $column) {
            $this->column($column, $alias);
        }

        return $this;
    }

    /**
     * @param mixed $name
     * @param null  $alias
     *
     * @return $this
     */
    public function column($name, $alias = null)
    {
        if ($alias === null) {
            $this->columns[] = $name;
        } else {
            $this->columns[$alias] = $name;
        }

        return $this;
    }

    /**
     * @param bool $value
     *
     * @return Select
     */
    public function distinct($value = true)
    {
        $this->distinct = $value;

        return $this;
    }

    /**
     * @param      $field
     * @param null $alias
     *
     * @return Select
     */
    public function min($field, $alias = null)
    {
        $this->column(new Func('min', $field), $alias);

        return $this;
    }

    /**
     * @param      $field
     * @param null $alias
     *
     * @return Select
     */
    public function max($field, $alias = null)
    {
        $this->column(new Func('max', $field), $alias);

        return $this;
    }

    /**
     * @param      $field
     * @param null $alias
     *
     * @return Select
     */
    public function avg($field, $alias = null)
    {
        $this->column(new Func('avg', $field), $alias);

        return $this;
    }

    /**
     * @param      $field
     * @param null $alias
     *
     * @return Select
     */
    public function sum($field, $alias = null)
    {
        $this->column(new Func('sum', $field), $alias);

        return $this;
    }

    /**
     * @return $this
     */
    public function groupBy()
    {
        $columns = func_get_args();

        if (empty($columns)) {
            return $this;
        }

        foreach ($columns as $column) {
            $this->groups[] = $column;
        }

        return $this;
    }

    /**
     * @param            $column
     * @param string     $operator
     * @param mixed|null $value
     *
     * @return Select
     */
    public function having($column, $operator = '=', $value = null)
    {
        if (func_num_args() === 2) {
            $value    = $operator;
            $operator = '=';
        }

        $this->having = [$column, $operator, $value];

        return $this;
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->execute();
    }

    /**
     * @return array
     */
    public function execute()
    {
        list($query, $params) = $this->sql();

        return (array)$this->connection->select($query, $params);
    }

    /**
     * @return null|array
     */
    public function row()
    {
        $this->limit(1);
        $results = $this->execute();

        return empty($results) ? null : reset($results);
    }

    /**
     * @return int
     */
    public function count()
    {
        $clone = clone $this;

        $clone->columns = [];
        $clone->column(new Func('count', new Expression('*')), 'total');
        $result = $clone->execute();

        return (int)current($result)['total'];
    }
}