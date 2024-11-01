<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Contracts\Database;
! defined( 'ABSPATH' ) && exit();


/**
 * Interface Connection
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Contracts\Database
 */
interface Connection
{
    /**
     * @return bool
     */
    public function connect(): bool;

    /**
     * @return bool
     */
    public function disconnect(): bool;

    /**
     * @return bool
     */
    public function isConnected(): bool;

    /**
     * @param string $sql
     * @param array  $data
     *
     * @return string|void
     */
    public function prepare($sql, array $data);

    /**
     * @param $data
     *
     * @return mixed
     */
    public function escape($data);

    /**
     * @param string $query
     * @param array  $data
     *
     * @return array
     */
    public function select($query, array $data = []);

    /**
     * @param       $query
     * @param array $data
     *
     * @return bool
     */
    public function insert($query, array $data = []): bool;

    /**
     * @param       $query
     * @param array $data
     *
     * @return int
     */
    public function replace($query, array $data = []): int;

    /**
     * @param       $query
     * @param array $data
     *
     * @return int
     */
    public function update($query, array $data = []): int;

    /**
     * @param       $query
     * @param array $data
     *
     * @return int
     */
    public function delete($query, array $data = []): int;

    /**
     * @param       $query
     * @param array $data
     *
     * @return mixed
     */
    public function execute($query, array $data = []);

    /**
     * @param $query
     *
     * @return bool
     */
    public function raw($query);
}