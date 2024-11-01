<?php

namespace TotalRating\Tasks\Rating;
! defined( 'ABSPATH' ) && exit();



use TotalRating\Models\Rating;
use TotalRatingVendors\TotalSuite\Foundation\Database\Query;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\DatabaseException;
use TotalRatingVendors\TotalSuite\Foundation\Support\Arrays;
use TotalRatingVendors\TotalSuite\Foundation\Support\Collection;
use TotalRatingVendors\TotalSuite\Foundation\Task;

/**
 * Class GetRatings
 *
 * @package TotalRating\Tasks\Rating
 * @method static Collection invoke($filters = [], $paginate = true)
 * @method static Collection invokeWithFallback($fallback, $filters = [], $paginate = true)
 */
class GetRatings extends Task
{
    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @var bool
     */
    protected $paginate = false;

    /**
     * constructor.
     *
     * @param  array  $filters
     * @param       $paginate
     */
    public function __construct($filters = [], $paginate = true)
    {
        $this->paginate = $paginate;

        $this->filters = Arrays::merge(
            [
                'widget_uid'  => null,
                'status'      => null,
                'from_date'   => null,
                'to_date'     => null,
                'entity_id'   => null,
                'entity_type' => null,
                'page'        => 1,
                'per_page'    => 100,
            ],
            (array) $filters
        );
    }


    /**
     * @inheritDoc
     */
    protected function validate()
    {
        return true;
    }

    /**
     * @return Collection
     * @throws DatabaseException
     */
    protected function execute()
    {
        /**
         * @var Query|Query\Select $query
         */
        $query = Rating::query()->select();

        if ($this->filters['widget_uid']) {
            $query->where('widget_uid', $this->filters['widget_uid']);
        }

        if ($this->filters['from_date']) {
            $query->where(Query::raw('DATE(created_at)'), '>=', $this->filters['from_date']);
        }

        if ($this->filters['to_date']) {
            $query->where(Query::raw('DATE(created_at)'), '<=', $this->filters['to_date']);
        }

        if ($this->filters['status']) {
            $filters = array_filter((array) $this->filters['status']);
            $query->whereIn('status', $filters);
        }

        if ($this->filters['entity_id']) {
            $query->where('entity_id', $this->filters['entity_id']);
        }

        if ($this->filters['entity_type']) {
            $query->where('entity_type', $this->filters['entity_type']);
        }

        $query->orderBy('created_at', 'desc');

        if ($this->paginate) {
            $this->filters['per_page'] = ($this->filters['per_page'] < 1) ? 10 : $this->filters['per_page'];

            return $query->paginate($this->filters['per_page'], $this->filters['page'] ?? 1);
        }

        return $query->get();
    }
}
