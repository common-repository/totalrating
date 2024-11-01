<?php

namespace TotalRating\Models;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Database\Model;
use TotalRatingVendors\TotalSuite\Foundation\Support\Collection;

/**
 * Class WorkflowRule
 *
 * @package TotalRating\Models
 *
 * @property bool                $enabled
 * @property string              $event
 * @property WorkflowCondition[] $conditions
 * @property WorkflowTask        $task
 */
class WorkflowRule extends Model
{
    /**
     * @var array
     */
    protected $types = [
        'conditions' => 'conditions',
        'task'       => 'task',
    ];

    /**
     * @var Widget
     */
    protected $widget;

    /**
     * constructor.
     *
     * @param Widget $widget
     * @param array  $attributes
     */
    public function __construct(Widget $widget, $attributes = [])
    {
        $this->widget = $widget;
        parent::__construct($attributes);
    }

    /**
     * @param mixed $task
     *
     * @return WorkflowTask
     * @noinspection PhpUnused
     */
    public function castToTask($task): WorkflowTask
    {
        return new WorkflowTask($this, $task);
    }

    /**
     * @param mixed $conditions
     *
     * @return Collection<WorkflowCondition>
     * @noinspection PhpUnused
     */
    public function castToConditions($conditions): Collection
    {
        $casted = [];
        foreach ($conditions as $condition) {
            $casted[] = new WorkflowCondition($this, $condition);
        }

        return Collection::create($casted);
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->getAttribute('enabled', false);
    }

    /**
     * @return string
     */
    public function getEvent(): string
    {
        return $this->getAttribute('event');
    }

    /**
     * @return WorkflowTask
     */
    public function getTask(): WorkflowTask
    {
        return $this->getAttribute('task');
    }

    /**
     * @return WorkflowCondition[]|Collection<WorkflowCondition>
     */
    public function getConditions(): Collection
    {
        return $this->getAttribute('conditions');
    }

    /**
     * @return Widget
     */
    public function getWidget(): Widget
    {
        return $this->widget;
    }
}
