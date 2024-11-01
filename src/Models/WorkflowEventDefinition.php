<?php

namespace TotalRating\Models;
! defined( 'ABSPATH' ) && exit();


use Composer\Script\Event;
use TotalRatingVendors\TotalSuite\Foundation\Database\Model;

/**
 * Class WorkflowEvent
 *
 * @package TotalRating\Models
 *
 * @property string       $id
 * @property Event|string $class
 * @property string       $label
 */
class WorkflowEventDefinition extends Model
{

}
