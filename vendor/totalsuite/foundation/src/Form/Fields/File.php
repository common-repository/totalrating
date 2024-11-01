<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Form\Fields;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Helpers\Html;

/**
 * Class File
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Form\Fields
 */
class File extends Text
{
    /**
     * @var array
     */
    protected $attributes = ['type' => 'file'];

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