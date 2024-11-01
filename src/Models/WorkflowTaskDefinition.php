<?php

namespace TotalRating\Models;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Tasks\Workflow\AbstractWorkflowTask;
use TotalRatingVendors\TotalSuite\Foundation\Database\Model;

/**
 * Class WorkflowTaskDefinition
 *
 * @package TotalRating\Models
 *
 * @property string                      $id
 * @property AbstractWorkflowTask|string $class
 * @property string                      $label
 */
class WorkflowTaskDefinition extends Model
{

}
