<?php

namespace TotalRating\Tasks\Workflow;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Models\WorkflowRule;
use TotalRatingVendors\TotalSuite\Foundation\Event;
use TotalRatingVendors\TotalSuite\Foundation\Task;

/**
 * Class WorkflowTask
 *
 * @package TotalRating\Tasks\Rating
 * @method static void invoke(WorkflowRule $rule, Event $event)
 * @method static void invokeWithFallback($fallback, WorkflowRule $rule, Event $event)
 */
abstract class AbstractWorkflowTask extends Task
{
    /**
     * @var WorkflowRule
     */
    protected $rule;

    /**
     * @var Event
     */
    protected $event;

    /**
     * constructor.
     *
     * @param WorkflowRule $rule
     * @param Event        $event
     */
    public function __construct(WorkflowRule $rule, Event $event)
    {
        $this->rule  = $rule;
        $this->event = $event;
    }


    /**
     * @inheritDoc
     */
    protected function validate()
    {
        return true;
    }
}
