<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Database;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Contracts\Database\Connection as ConnectionInterface;
use TotalRatingVendors\TotalSuite\Foundation\Database\Grammar\MySQL;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\DatabaseException;

/**
 * Class Connection
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Database
 */
abstract class Connection implements ConnectionInterface
{
    /**
     * @var string
     */
    protected $database;

    /**
     * @var string
     */
    protected $tablePrefix = '';

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var Grammar
     */
    protected $grammar;

    /**
     * Connection constructor.
     *
     * @param string $database
     * @param string $tablePrefix
     * @param array  $options
     */
    public function __construct(string $database, string $tablePrefix = '', array $options = [])
    {
        $this->database    = $database;
        $this->options     = $options;
        $this->tablePrefix = $tablePrefix;
        $this->setGrammar(new MySQL($tablePrefix));
    }

    /**
     * @return string
     */
    public function getDatabase(): string
    {
        return $this->database;
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
        $this->getGrammar()->setTablePrefix($tablePrefix);
    }

    /**
     * @return Grammar
     */
    public function getGrammar(): Grammar
    {
        return $this->grammar;
    }

    /**
     * @param Grammar $grammar
     */
    public function setGrammar(Grammar $grammar)
    {
        $this->grammar = $grammar;
    }

    /**
     * @param $tableName
     *
     * @return Query
     * @throws DatabaseException
     */
    public function table($tableName)
    {
        return new Query($this, $tableName);
    }
}