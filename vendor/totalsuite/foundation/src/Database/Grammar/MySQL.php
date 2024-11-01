<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Database\Grammar;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Database\Grammar;
use TotalRatingVendors\TotalSuite\Foundation\Database\Query\BaseQuery;
use TotalRatingVendors\TotalSuite\Foundation\Database\Query\Delete;
use TotalRatingVendors\TotalSuite\Foundation\Database\Query\Exists;
use TotalRatingVendors\TotalSuite\Foundation\Database\Query\Expression;
use TotalRatingVendors\TotalSuite\Foundation\Database\Query\Insert;
use TotalRatingVendors\TotalSuite\Foundation\Database\Query\Select;
use TotalRatingVendors\TotalSuite\Foundation\Database\Query\Update;

/**
 * Class MySQL
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Database\Grammar
 */
class MySQL extends Grammar
{
    /**
     * @inheritDoc
     */
    public function process(BaseQuery $query): array
    {
        $this->attributes = $query->attributes();
        $this->values     = [];
        $sql              = '';

        if ($query instanceof Select) {
            $sql = $this->processSelect();
        }

        if ($query instanceof Insert) {
            $sql = $this->processInsert();
        }

        if ($query instanceof Update) {
            $sql = $this->processUpdate();
        }

        if ($query instanceof Delete) {
            $sql = $this->processDelete();
        }

        if ($query instanceof Exists) {
            $sql = $this->processExists();
        }

        return [$sql, $this->values];
    }

    /**
     * @return string
     */
    public function processSelect(): string
    {
        $_sql = [$this->getAttribute('distinct') ? 'SELECT DISTINCT' : 'SELECT'];

        $_sql[] = $this->processColumns();

        $_sql[] = $this->processTable();

        $wheres = $this->processWhere($this->getAttribute('wheres', []));
        $_sql[] = empty($wheres) ? '' : 'WHERE ' . $wheres;

        $_sql[] = $this->processGroupBy();

        $_sql[] = $this->processHaving();

        $_sql[] = $this->processOrder();

        $_sql[] = $this->processLimit();

        return implode(' ', array_filter($_sql));
    }

    /**
     * @return string
     */
    protected function processColumns()
    {
        $columns = (array)$this->getAttribute('columns', []);

        if (!empty($columns)) {
            $fields = [];

            foreach ($columns as $alias => $column) {
                if ($column === '*') {
                    $column = new Expression($column);
                    $alias  = null;
                }

                if (is_string($alias)) {
                    $fields[] = $this->escape($column) . ' AS ' . $this->escape($alias);
                } else {
                    $fields[] = $this->escape($column);
                }
            }

            return implode(', ', $fields);
        }

        return '*';
    }

    /**
     * @return string
     */
    protected function processTable()
    {
        $table = (array)$this->getAttribute('table');

        if ($table[0] instanceof Select) {
            list($subQuery, $subQueryValues) = (new static($this->tablePrefix))->process($table[0]);
            $this->addValue($subQueryValues);
            return 'FROM (' . $subQuery . ') AS ' . $this->escape($table[1]);
        }

        if ($table[0] instanceof Expression) {
            return 'FROM (' . $table[0]->getValue() . ') AS ' . $this->escape($table[1]);
        }

        if (isset($table[1])) {
            return 'FROM ' . $this->escape($this->tablePrefix . $table[0]) . ' AS ' . $this->escape($table[1]);
        }

        return 'FROM ' . $this->escape($this->tablePrefix . $table[0]);
    }

    /**
     * return [wheres[], bindings[]]
     *
     * @param array $wheres
     *
     * @return string
     */
    protected function processWhere(array $wheres)
    {
        if (empty($wheres)) {
            return '';
        }

        $conditions = [];

        foreach ($wheres as $where) {
            if ($where[1] instanceof Exists) {
                list($existsQuery, $existsData) = (new static($this->tablePrefix))->process(
                    $where[1]->attributes()['select']
                );
                $conditions[] = strtoupper($where[0]) . ' ' . strtoupper($where[2]) . ' (' . $existsQuery . ')';
                $this->addValue($existsData);
            } elseif ($where[1] instanceof Select) {
                $whereGroup = $this->processWhere($where[1]->attributes()['wheres']);

                if (!empty($whereGroup)) {
                    $conditions[] = strtoupper($where[0]) . ' (' . $whereGroup . ')';
                }
            } else {
                $conditions[] = strtoupper($where[0]) . ' ' . $this->escape($where[1]) . ' ' . strtoupper(
                        $where[2]
                    ) . ' ' . $this->bindParam($where[3]);
            }
        }

        return preg_replace('/AND |OR /i', '', implode(' ', $conditions), 1);
    }

    /**
     * @return string
     */
    protected function processGroupBy()
    {
        $groups = $this->getAttribute('groups');

        if (empty($groups)) {
            return '';
        }

        foreach ($groups as &$group) {
            $group = $this->escape($group);
        }

        return 'GROUP BY ' . implode(', ', $groups);
    }

    /**
     * @return string
     */
    protected function processHaving()
    {
        $having = $this->getAttribute('having');

        if (empty($having)) {
            return '';
        }

        if ($having[2] instanceof Select) {
            list($select, $selectValues) = (new static($this->tablePrefix))->process($having[2]);
            $having[2] = '(' . $select . ')';
            $this->addValue($selectValues);
        } else {
            $having[2] = $this->bindParam($having[2]);
        }

        return 'HAVING ' . $this->escape($having[0]) . ' ' . $having[1] . ' ' . $having[2];
    }

    /**
     * @return string
     */
    protected function processOrder()
    {
        $orders = $this->getAttribute('orders');

        if (empty($orders)) {
            return '';
        }

        $translated = [];

        foreach ($orders as list($column, $direction)) {
            if ($column instanceof Expression) {
                $translated[] = $column->getValue();
            } else {
                $translated[] = $this->escape($column) . ' ' . strtoupper($direction);
            }
        }

        return 'ORDER BY ' . implode(', ', $translated);
    }

    /**
     * @return string
     */
    protected function processLimit()
    {
        $limit  = $this->getAttribute('limit');
        $offset = $this->getAttribute('offset');

        if ($limit) {
            if ($offset !== null) {
                return 'LIMIT ' . $this->bindParam($offset) . ', ' . $this->bindParam($limit);
            }

            return 'LIMIT ' . $this->bindParam($limit);
        }

        return '';
    }

    /**
     * @return string
     */
    public function processInsert(): string
    {
        $_sql = [$this->getAttribute('ignore', false) ? 'INSERT IGNORE' : 'INSERT'];

        $_sql[] = 'INTO ' . $this->escape($this->tablePrefix . $this->getAttribute('table'));

        $values = $this->getAttribute('values', []);

        $columns = [];
        $sets    = [];

        foreach ($values as $column => $value) {
            $columns[] = $this->escape($column);
            $sets[]    = $this->bindParam($value);
        }

        $_sql[] = '(' . implode(', ', $columns) . ')';
        $_sql[] = 'VALUES (' . implode(', ', $sets) . ')';

        return implode(' ', array_filter($_sql));
    }

    /**
     * @return string
     */
    public function processUpdate(): string
    {
        $_sql   = ['UPDATE'];
        $_sql[] = $this->escape($this->tablePrefix . $this->getAttribute('table'));

        $values = $this->getAttribute('values', []);
        $sets   = [];

        foreach ($values as $column => $value) {
            $sets[] = $this->escape($column) . ' = ' . $this->bindParam($value);
        }

        $_sql[] = 'SET ' . implode(', ', $sets);

        $wheres = $this->processWhere($this->getAttribute('wheres', []));
        $_sql[] = empty($wheres) ? '' : 'WHERE ' . $wheres;

        $_sql[] = $this->processOrder();
        $_sql[] = $this->processLimit();

        return implode(' ', array_filter($_sql));
    }

    /**
     * @return string
     */
    public function processDelete(): string
    {
        $_sql   = ['DELETE'];
        $_sql[] = 'FROM ' . $this->escape($this->tablePrefix . $this->getAttribute('table'));

        $wheres = $this->processWhere($this->getAttribute('wheres', []));
        $_sql[] = empty($wheres) ? '' : 'WHERE ' . $wheres;

        $_sql[] = $this->processOrder();
        $_sql[] = $this->processLimit();

        return implode(' ', array_filter($_sql));
    }

    /**
     * @return string
     */
    protected function processExists()
    {
        $exists = (bool)$this->getAttribute('exists');
        $_sql   = ['SELECT'];
        list($subQuery, $subQueryValues) = (new static($this->tablePrefix))->process($this->getAttribute('select'));
        $_sql[] = ($exists ? 'EXISTS' : 'NOT EXISTS') . ' (' . $subQuery . ') AS ' . $this->escape('exists');
        $this->addValue($subQueryValues);

        return implode(' ', array_filter($_sql));
    }

    /**
     * @inheritDoc
     */
    public function bind($value)
    {
        if (is_int($value) || is_bool($value)) {
            return '%d';
        }

        if (is_float($value)) {
            return '%f';
        }

        return '%s';
    }
}