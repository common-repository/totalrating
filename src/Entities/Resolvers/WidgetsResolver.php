<?php

namespace TotalRating\Entities\Resolvers;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Contracts\EntityResolver;
use TotalRating\Entities\Entity;
use TotalRating\Models\Widget;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\DatabaseException;

class WidgetsResolver implements EntityResolver
{

    protected $type = 'widget';

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param       $id
     * @param       $subtype
     * @param array $meta
     *
     * @return Entity
     */
    public function resolve($id, $subtype = null, array $meta = [])
    {
        try {
            $widget = Widget::byUID($id);

            return new Entity($widget->uid, $widget->name, $this->type, null, null);
        } catch (DatabaseException $e) {
            return null;
        }
    }
}
