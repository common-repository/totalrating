<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Database\Query\Concerns;
! defined( 'ABSPATH' ) && exit();



use TotalRatingVendors\TotalSuite\Foundation\Database\Query;
use TotalRatingVendors\TotalSuite\Foundation\Database\Query\Exists;
use TotalRatingVendors\TotalSuite\Foundation\Database\Query\Expression;
use TotalRatingVendors\TotalSuite\Foundation\Database\Query\Func;
use TotalRatingVendors\TotalSuite\Foundation\Database\Query\Select;

/**
 * Trait Where
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Database\Query\Concerns
 */
trait Where
{
    /**
     * @var array
     */
    protected $wheres = [];

    /**
     * @param        $column
     * @param string $operator
     * @param null   $value
     *
     * @return static
     */
    public function orWhere($column, $operator = '=', $value = null)
    {
        return $this->where($column, $operator, $value, 'or');
    }

    /**
     * @param string|array|callable $column
     * @param string                $operator
     * @param mixed                 $value
     * @param string                $mode
     *
     * @return static
     */
    public function where($column, $operator = '=', $value = null, $mode = 'and')
    {
        if (is_array($column)) {
            foreach ($column as $key => $param) {
                $this->where($key, $operator, $param, $mode);
            }

            return $this;
        }

        if (is_callable($column)) {
            $query = new Select();
            $column($query);
            $this->wheres[] = [$mode, $query];

            return $this;
        }

        if ($column instanceof Exists) {
            $this->wheres[] = [$mode, $column, $operator];
            return $this;
        }

        if ($value === null) {
            $value    = $operator;
            $operator = '=';
        }

        $this->wheres[] = [$mode, $column, $operator, is_array($value) ? array_unique($value) : $value];

        return $this;
    }

    /**
     * @param       $column
     * @param array $options
     *
     * @return static
     */
    public function whereIn($column, array $options = [])
    {
        return empty($options) ? $this : $this->where($column, 'in', $options);
    }

    /**
     * @param       $column
     * @param array $options
     *
     * @return static
     */
    public function orWhereIn($column, array $options = [])
    {
        return empty($options) ? $this : $this->where($column, 'in', $options, 'or');
    }

    /**
     * @param       $column
     * @param array $options
     *
     * @return static
     */
    public function whereNotIn($column, array $options = [])
    {
        return empty($options) ? $this : $this->where($column, 'not in', $options);
    }

    /**
     * @param       $column
     * @param array $options
     *
     * @return static
     */
    public function orWhereNotIn($column, array $options = [])
    {
        return empty($options) ? $this : $this->where($column, 'not in', $options, 'or');
    }

    /**
     * @param $column
     *
     * @return static
     */
    public function whereNull($column)
    {
        return $this->where($column, 'is', Query::raw('NULL'));
    }

    /**
     * @param $column
     *
     * @return static
     */
    public function orWhereNull($column)
    {
        return $this->where($column, 'is', Query::raw('NULL'), 'or');
    }

    /**
     * @param $column
     *
     * @return static
     */
    public function whereNotNull($column)
    {
        return $this->where($column, 'is not', Query::raw('NULL'));
    }

    /**
     * @param $column
     *
     * @return static
     */
    public function orWhereNotNull($column)
    {
        return $this->where($column, 'is not', Query::raw('NULL'), 'or');
    }

    /**
     * @param callable $callback
     *
     * @return $this
     */
    public function whereExists(callable $callback)
    {
        $query = (new Exists())->exists($callback);
        $this->where($query, 'exists', 'and');

        return $this;
    }

    /**
     * @param callable $callback
     *
     * @return Where
     */
    public function whereNotExists(callable $callback)
    {
        $query = (new Exists())->exists($callback, false);
        $this->where($query, 'not exists', 'and');

        return $this;
    }

    /**
     * @param Expression|Func|string $column
     * @param string                 $operator
     * @param null                   $date
     *
     * @return $this
     */
    public function whereDate($column, $operator = '=', $date = null)
    {
        $this->where(new Func('DATE', $column), $operator, $date);
        return $this;
    }

    /**
     * @param Expression|Func|string $column
     * @param string                 $operator
     * @param null                   $time
     *
     * @return $this
     */
    public function whereTime($column, $operator = '=', $time = null)
    {
        $this->where(new Func('TIME', $column), $operator, $time);
        return $this;
    }

    /**
     * @param Expression|Func|string $column
     * @param string                 $operator
     * @param null                   $datetime
     *
     * @return $this
     */
    public function whereDateTime($column, $operator = '=', $datetime = null)
    {
        $this->where(new Func('DATETIME', $column), $operator, $datetime);
        return $this;
    }
}