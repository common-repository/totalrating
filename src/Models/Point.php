<?php

namespace TotalRating\Models;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Filters\FilterPointStatistics;
use TotalRatingVendors\TotalSuite\Foundation\Database\Model;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\DatabaseException;

/**
 * @property string           $uid
 * @property int              $value
 * @property string           $label
 * @property array            $symbol
 * @property int              $total
 * @property array statistics
 */
class Point extends Model
{
    /**
     * @var Attribute
     */
    protected $attribute;

    /**
     * constructor.
     *
     * @param Attribute $attribute
     * @param           $attributes
     */
    public function __construct(Attribute $attribute, $attributes)
    {
        $this->attribute = $attribute;
        parent::__construct($attributes);
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->getAttribute('total', 0);
    }

    /**
     * @return array
     */
    public function getSymbol(): array
    {
        return $this->getAttribute('symbol', []);
    }

    /**
     * @param array $symbol
     */
    public function setSymbol(array $symbol)
    {
        $this->setAttribute('symbol', $symbol);
    }

    /**
     *
     * @return $this
     * @throws DatabaseException
     */
    public function withStatistics() {
        $this->statistics = [
            'total' => $this->total()
        ];

        $this->setAttribute('statistics', FilterPointStatistics::apply($this->statistics, $this, $this->attribute));

        return $this;
    }

    /**
     * @return int
     */
    public function total() {
        return (int) Rating::query()
               ->where('attribute_uid', $this->attribute->uid)
               ->where('point_uid', $this->uid)
               ->where('status', Rating::STATUS_ACCEPTED)
               ->count();
    }
}
