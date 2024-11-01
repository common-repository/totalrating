<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Database\Query;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Database\Query;
use TotalRatingVendors\TotalSuite\Foundation\Support\Collection;

class Pagination extends Collection
{
    /**
     * @var Select
     */
    protected $query;

    /**
     * @var int
     */
    protected $total = 0;

    /**
     * @var int
     */
    protected $current = 0;

    /**
     * @var int
     */
    protected $perPage = 10;

    /**
     * @var int
     */
    protected $numPages = 0;

    /**
     * @var array
     */
    protected $items = [];

    /**
     * Override fresh to return current collection instead of a fresh one.
     * This will make helpers such as where not conflicting.
     *
     * @param array $items
     * @return $this|Pagination
     */
    protected function fresh($items = [])
    {
        return $this;
    }

    /**
     * @param Select|Query $query
     * @param $perPage
     * @param $current
     *
     * @return static
     */
    protected function prepareFromQuery(Query $query, $perPage, $current)
    {
        $this->query = $query;
        $this->total = $query->count();
        $this->perPage = ($perPage < 1) ? 1 : $perPage;
        $this->current = ($current < 1) ? 1 : $current;
        $this->numPages = ceil($this->total / $this->perPage);

        $items = $query->page($perPage, $current)->get()->all();
        $this->fill($items);

        return $this;
    }

    /**
     * @param Query|Select $query
     * @param int $perPage
     * @param int $current
     *
     * @return static
     */
    public static function fromQuery(Query $query, $perPage = 10, $current = 1)
    {
        return (new static())->prepareFromQuery($query, $perPage, $current);
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'pagination' => [
                'total' => $this->getTotal(),
                'current' => $this->getCurrent(),
                'per_page' => $this->getPerPage(),
                'pages' => $this->getNumPages(),
                'found' => count($this->getItems()),
            ],
            'items' => $this->getItems(),
        ];
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @return int
     */
    public function getCurrent(): int
    {
        return $this->current;
    }

    /**
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * @return int
     */
    public function getNumPages(): int
    {
        return $this->numPages;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }
}