<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Database\Query;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Contracts\Database\Connection;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\DatabaseException;
use TotalRatingVendors\TotalSuite\Foundation\Support\Arrays;

/**
 * Class Base
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Database\Query
 */
abstract class BaseQuery
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var string | Select | array
     */
    protected $table;

    /**
     * Base constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection = null)
    {
        $this->connection = $connection;
    }

    /**
     * @param string|Expression|Select $table
     * @param string|null              $alias
     *
     * @return $this
     * @throws DatabaseException
     */
    public function table($table, $alias = null)
    {
        if (($table instanceof Select || $table instanceof Expression) && $alias === null) {
            throw new DatabaseException('Table must have an alias when provided as subquery');
        }

        if (!empty($alias)) {
            $this->table = [$table, $alias];
        } else {
            $this->table = $table;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return Arrays::except(get_object_vars($this), ['connection']);
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        list($query, $params) = $this->sql();
        return $this->connection->execute($query, $params);
    }

    /**
     * @return array
     */
    public function sql()
    {
        return $this->connection->getGrammar()->process($this);
    }

    /**
     * @return string|void
     */
    public function prepare()
    {
        list($query, $params) = $this->sql();
        return $this->connection->prepare($query, $params);
    }
}