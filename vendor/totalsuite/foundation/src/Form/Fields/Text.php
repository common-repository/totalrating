<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Form\Fields;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Form\Field;
use TotalRatingVendors\TotalSuite\Foundation\Helpers\Html;

/**
 * Class Text
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Form\Fields
 */
class Text extends Field
{
    /**
     * @var array
     */
    protected $attributes = ['type' => 'text'];

    /**
     * @return Html
     */
    public function toHTML()
    {
        return Html::create('input', $this->getHtmlAttributes());
    }
}