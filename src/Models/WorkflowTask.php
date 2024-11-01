<?php

namespace TotalRating\Models;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Database\Model;
use TotalRatingVendors\TotalSuite\Foundation\Support\Collection;

/**
 * Class WorkflowTask
 *
 * @package TotalRating\Models
 *
 * @property string     $id
 * @property Collection $arguments
 */
class WorkflowTask extends Model
{
    /**
     * @var array
     */
    protected $types = [
        'arguments' => 'arguments',
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
     * @param mixed $arguments
     *
     * @return Collection
     * @noinspection PhpUnused
     */
    public function castToArguments($arguments): Collection
    {
        return Collection::create($arguments);
    }
}
