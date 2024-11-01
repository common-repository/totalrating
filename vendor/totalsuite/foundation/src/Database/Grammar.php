<?php


namespace TotalRatingVendors\TotalSuite\Foundation\Database;
! defined( 'ABSPATH' ) && exit();



use TotalRatingVendors\TotalSuite\Foundation\Database\Query\BaseQuery;
use TotalRatingVendors\TotalSuite\Foundation\Database\Query\Expression;
use TotalRatingVendors\TotalSuite\Foundation\Database\Query\Func;

/**
 * Class Grammar
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Database
 */
abstract class Grammar
{
    /**
     * @var string
     */
    protected $tablePrefix = '';

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var array
     */
    protected $values = [];

    /**
     * Grammar constructor.
     *
     * @param string $tablePrefix
     */
    public function __construct(string $tablePrefix = '')
    {
        $this->tablePrefix = $tablePrefix;
    }

    /**
     * @return string
     */
    public function getTablePrefix(): string
    {
        return $this->tablePrefix;
    }

    /**
     * @param string $tablePrefix
     */
    public function setTablePrefix(string $tablePrefix)
    {
        $this->tablePrefix = $tablePrefix;
    }

    /**
     * Return [string $sql, array $values]
     *
     * @param BaseQuery $query
     *
     * @return array
     */
    abstract public function process(BaseQuery $query);

    /**
     * @param      $name
     * @param null $default
     *
     * @return mixed|null
     */
    protected function getAttribute($name, $default = null)
    {
        return $this->attributes[$name] ?? $default;
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    protected function bindParam($value)
    {
        if ($value instanceof Expression) {
            return $value->getValue();
        }

        if ($value instanceof Func) {
            return $this->escapeFunction($value);
        }

        if (is_array($value)) {
            $params = [];

            foreach ($value as $v) {
                $params[] = $this->bind($v);
                $this->addValue($v);
            }

            return '(' . implode(', ', $params) . ')';
        }

        $this->addValue($value);

        return $this->bind($value);
    }

    /**
     * @param Func $f
     *
     * @return string
     */
    protected function escapeFunction(Func $f)
    {
        $arguments = $f->getArguments();

        foreach ($arguments as &$argument) {
            if ($argument instanceof Expression) {
                $argument = $argument->getValue();
            } elseif ($argument instanceof Func) {
                $argument = $this->escapeFunction($argument);
            } else {
                $argument = $this->escape($argument);
            }
        }

        return strtoupper($f->getName()) . '(' . implode(',', $arguments) . ')';
    }

    /**
     * @param $value
     *
     * @return mixed|void
     */
    protected function escape($value)
    {
        if ($value instanceof Expression) {
            return $value->getValue();
        }

        if ($value instanceof Func) {
            return $this->escapeFunction($value);
        }

        if (strpos($value, '.') !== false) {
            $value = array_map(
                function ($item) {
                    return $this->escapeIdentifier($item);
                },
                explode('.', $value)
            );

            $value = implode('.', $value);
        }

        return $this->escapeIdentifier($value);
    }

    /**
     * @param string $column
     *
     * @return string
     */
    protected function escapeIdentifier($column)
    {
        return '`' . str_replace(['`', "\0"], ['``', ''], $column) . '`';
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    abstract public function bind($value);

    /**
     * @param mixed $value
     */
    protected function addValue($value)
    {
        if (is_array($value)) {
            foreach ($value as $v) {
                if ($v instanceof Expression) {
                    $this->values[] = $v->getValue();
                } elseif ($v instanceof Func) {
                    $this->values[] = $this->escapeFunction($v);
                } else {
                    $this->values [] = $v;
                }
            }
        } else {
            $this->values[] = $value;
        }
    }
}