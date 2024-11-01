<?php

namespace TotalRatingVendors\TotalSuite\Foundation\WordPress\Database;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Contracts\Database\LastInsertId;
use TotalRatingVendors\TotalSuite\Foundation\Database\Connection;
use wpdb;

/**
 * Class Connection
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\WordPress\Database
 */
class WPConnection extends Connection implements LastInsertId
{
    /**
     * @var wpdb
     */
    protected $wpdb;

    /**
     * @var string
     */
    protected $defaultFetchMode = ARRAY_A;

    /**
     * WPConnection constructor.
     *
     * @param string $database
     * @param string $tablePrefix
     * @param array  $options
     */
    public function __construct(string $database, string $tablePrefix = '', array $options = [])
    {
        parent::__construct($database, $tablePrefix, $options);
        $this->connect();
    }

    /**
     * @return bool
     */
    public function connect(): bool
    {
        $this->wpdb = $GLOBALS['wpdb'];
        return $this->isConnected();
    }

    /**
     * @inheritDoc
     */
    public function isConnected(): bool
    {
        return $this->wpdb instanceof wpdb;
    }

    /**
     * @return bool
     */
    public function disconnect(): bool
    {
        return true;
    }

    /**
     * @param mixed $data
     *
     * @return array|string
     */
    public function escape($data)
    {
        return $this->wpdb->_escape($data);
    }

    /**
     * @inheritDoc
     */
    public function select($query, array $data = [], $fetchMode = null)
    {
        $preparedQuery = $this->prepare($query, $data);
        return $this->wpdb->get_results($preparedQuery, $this->getDefaultFetchMode($fetchMode));
    }

    /**
     * @param string $query
     * @param array $data
     *
     * @return string|void
     */
    public function prepare($query, array $data = [])
    {
        // Handle NULL
        foreach ($data as $index => $datum) {
            if (is_null($datum)) {
                $data[$index] = 'NULL';
            }
        }

        $query = empty($data) ? $query : $this->wpdb->prepare($query, $data);
        return str_replace("'NULL'", 'NULL', $query);
    }

    /**
     * @param null $fetchMode
     *
     * @return string
     */
    public function getDefaultFetchMode($fetchMode = null)
    {
        if ($fetchMode === null) {
            return $this->defaultFetchMode;
        }

        return (string)$fetchMode;
    }

    /**
     * @param string $defaultFetchMode
     */
    public function setDefaultFetchMode(string $defaultFetchMode)
    {
        $this->defaultFetchMode = $defaultFetchMode;
    }

    /**
     * @inheritDoc
     */
    public function replace($query, array $data = []): int
    {
        return $this->execute($query, $data);
    }

    /**
     * @inheritDoc
     */
    public function execute($query, array $data = [])
    {
        $preparedQuery = $this->prepare($query, $data);
        return $this->wpdb->query($preparedQuery);
    }

    /**
     * @inheritDoc
     */
    public function update($query, array $data = []): int
    {
        return $this->execute($query, $data);
    }

    /**
     * @inheritDoc
     */
    public function delete($query, array $data = []): int
    {
        return $this->execute($query, $data);
    }

    /**
     * @inheritDoc
     */
    public function raw($query)
    {
        return $this->wpdb->query($query);
    }

    /**
     * @inheritDoc
     */
    public function lastInsertId(): int
    {
        return $this->wpdb->insert_id;
    }

    /**
     * @param string $query
     * @param array  $data
     *
     * @return int|null
     */
    public function insertGetId($query, array $data = []): int
    {
        if ($this->insert($query, $data) !== false) {
            return $this->wpdb->insert_id;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function insert($query, array $data = []): bool
    {
        return $this->execute($query, $data);
    }

    /**
     * @param string[]|string $queries
     * @param bool            $execute
     *
     * @return array|bool
     */
    public static function bulk($queries, $execute = true)
    {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        return dbDelta($queries, $execute);
    }
}