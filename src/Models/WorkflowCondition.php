<?php

namespace TotalRating\Models;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Database\Model;

/**
 * Class WorkflowCondition
 *
 * @package TotalRating\Models
 *
 * @property string    $attributeUid
 * @property string    $pointUid
 * @property string    $operator
 * @property Attribute $attribute
 * @property Point     $point
 */
class WorkflowCondition extends Model
{
    const OPERATOR_LESS_THAN = 'lessThan';
    const OPERATOR_EQUALS = 'equals';
    const OPERATOR_GREATER_THAN = 'greaterThan';

    /**
     * @var array
     */
    protected $types = [
        'attributeUid' => 'attribute',
        'pointUid'     => 'point',
    ];

    /**
     * @var WorkflowRule
     */
    protected $rule;

    /**
     * constructor.
     *
     * @param WorkflowRule $rule
     * @param array        $attributes
     */
    public function __construct(WorkflowRule $rule, $attributes = [])
    {
        $this->rule = $rule;
        parent::__construct($attributes);
    }

    /**
     * @param string $attributeUid
     *
     * @return string
     * @noinspection PhpUnused
     */
    public function castToAttribute($attributeUid): string
    {
        $attribute = $this->rule->getWidget()
                                ->getRatingAttribute($attributeUid);

        if ($attribute) {
            $this->setAttribute('attribute', $attribute);
        }

        return $attributeUid;
    }

    /**
     * @param string $pointUid
     *
     * @return string
     * @noinspection PhpUnused
     */
    public function castToPoint($pointUid): string
    {
        if ($this->attribute) {
            $this->setAttribute('point', $this->attribute->getPoint($pointUid));
        }

        return $pointUid;
    }

    /**
     * @return bool
     */
    public function isOperatorEquals(): bool
    {
        return $this->getAttribute('operator') === self::OPERATOR_EQUALS;
    }

    /**
     * @return bool
     */
    public function isOperatorLessThan(): bool
    {
        return $this->getAttribute('operator') === self::OPERATOR_LESS_THAN;
    }

    /**
     * @return bool
     */
    public function isOperatorGreaterThan(): bool
    {
        return $this->getAttribute('operator') === self::OPERATOR_GREATER_THAN;
    }

    /**
     * @return string
     */
    public function getOperator(): string
    {
        return $this->getAttribute('operator');
    }

    /**
     * @return Point
     */
    public function getPoint()
    {
        return $this->getAttribute('point');
    }

    /**
     * @return Attribute
     */
    public function getRatingAttribute()
    {
        return $this->getAttribute('attribute');
    }
}
