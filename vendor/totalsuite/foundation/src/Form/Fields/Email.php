<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Form\Fields;
! defined( 'ABSPATH' ) && exit();


/**
 * Class Email
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Form\Fields
 */
class Email extends Text
{
    /**
     * @var array
     */
    protected $attributes = ['type' => 'email'];
}