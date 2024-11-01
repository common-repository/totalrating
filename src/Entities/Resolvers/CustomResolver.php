<?php

namespace TotalRating\Entities\Resolvers;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Contracts\EntityResolver;
use TotalRating\Entities\Entity;

class CustomResolver implements EntityResolver
{

    protected $type = 'custom';

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
     * @param  array  $meta
     *
     * @return Entity
     */
    public function resolve($id, $subtype = null, array $meta = [])
    {
        $id      = esc_html($id);
        $subtype = esc_html($subtype);

        return new Entity($id, $id, $subtype ? "$this->type:$subtype" : $this->type, esc_html__('Custom', 'totalrating')." ({$subtype})", null);
    }
}
