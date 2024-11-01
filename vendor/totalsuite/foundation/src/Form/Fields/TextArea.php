<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Form\Fields;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Form\Field;
use TotalRatingVendors\TotalSuite\Foundation\Helpers\Html;


/**
 * Class TextArea
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Form\Fields
 */
class TextArea extends Field
{
    /**
     * @return Html
     */
    public function toHTML()
    {
        return Html::create('textarea', $this->getHtmlAttributes(['value']), $this->getValue());
    }
}