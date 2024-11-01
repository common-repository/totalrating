<?php

namespace TotalRating\Entities\Resolvers;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Contracts\EntityResolver;
use TotalRating\Entities\Entity;

class TaxonomiesResolver implements EntityResolver
{

    /**
     * @var string
     */
    protected $type = 'taxonomy';

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
        $term = get_term((int)$id);

        if ($term) {
            $url = get_term_link($term);
            $taxonomy = get_taxonomy($term->taxonomy);

            return new Entity($term->term_id, $term->name, $this->type, $taxonomy->label, $url);
        }

        return null;
    }
}