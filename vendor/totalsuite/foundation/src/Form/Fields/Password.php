<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Form\Fields;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Helpers\Html;

/**
 * Class Password
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Form\Fields
 */
class Password extends Text
{
    /**
     * @var array
     */
    protected $attributes = ['type' => 'password'];

    /**
     * @return Html
     */
    public function toHTML()
    {
        $html = parent::toHTML();
        $html->removeAttribute('value');

        return $html;
    }


}