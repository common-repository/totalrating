<?php

namespace TotalRating\Tasks\Widget;
! defined( 'ABSPATH' ) && exit();



use TotalRating\Entities\Entity;
use TotalRating\Models\Attribute;
use TotalRating\Models\Rating;
use TotalRating\Models\Widget;
use TotalRatingVendors\TotalSuite\Foundation\Http\CookieJar;
use TotalRatingVendors\TotalSuite\Foundation\Task;

/**
 * Class CheckResultsVisibilityForAttribute
 *
 * @package TotalRating\Tasks\Widget
 * @method static bool invoke(Widget $widget, Attribute $attribute, Entity $entity = null)
 * @method static bool invokeWithFallback($fallback, Widget $widget, Attribute $attribute, Entity $entity = null)
 */
class CanViewResultsOfAttribute extends Task
{
    /**
     * @var Widget
     */
    protected $widget;

    /**
     * @var Attribute
     */
    protected $attribute;

    /**
     * @var Entity
     */
    protected $entity;

    /**
     * constructor.
     *
     * @param  Widget  $widget
     * @param  Attribute  $attribute
     */
    public function __construct(Widget $widget, Attribute $attribute, Entity $entity = null)
    {
        $this->widget    = $widget;
        $this->attribute = $attribute;
        $this->entity    = $entity;
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
        if ($this->widget->isResultsHiddenForAll()) {
            return false;
        }

        if ($this->widget->isResultsHiddenForNonVoters()) {
            if ($this->entity && !$this->attribute->withHelpers($this->entity)->canRate) {
                return true;
            }

            return CookieJar::instance()
                            ->has(
                                Rating::getCookieName(
                                    'attribute',
                                    $this->attribute->uid,
                                    $this->entity ? $this->entity->getType() : '',
                                    $this->entity ? $this->entity->getId() : ''
                                )
                            );
        }

        return true;
    }
}
