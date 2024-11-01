<?php

namespace TotalRating\Tasks\Widget;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Entities\Entity;
use TotalRating\Models\Attribute;
use TotalRating\Models\Rating;
use TotalRating\Models\Widget;
use TotalRatingVendors\TotalSuite\Foundation\Http\CookieJar;
use TotalRatingVendors\TotalSuite\Foundation\Task;

class CanViewResultsOfWidget extends Task
{
    /**
     * @var Widget
     */
    protected $widget;
    /**
     * @var Entity|null
     */
    protected $entity;

    /**
     * constructor.
     *
     * @param  Widget  $widget
     * @param  Entity|null  $entity
     */
    public function __construct(Widget $widget, Entity $entity = null)
    {
        $this->widget = $widget;
        $this->entity = $entity;
    }


    /**
     * @inheritDoc
     */
    protected function validate()
    {
        return true;
    }

    /**
     * @return bool
     */
    protected function execute()
    {
        if ($this->widget->isResultsVisible()) {
            return true;
        }

        if ($this->widget->isResultsHiddenForAll()) {
            return false;
        }

        if ($this->widget->isResultsHiddenForNonVoters()) {
            foreach ($this->widget->getRatingAttributes() as $attribute) {
                /**
                 * @var $attribute Attribute
                 */
                if ($this->entity && !$attribute->withHelpers($this->entity)->canRate) {
                    return true;
                }
            }

            return CookieJar::instance()->has(Rating::getCookieName('widget', $this->widget->uid, '', ''));
        }

        return false;
    }
}
