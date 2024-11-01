<?php

namespace TotalRating\Entities\Resolvers;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Contracts\EntityResolver;
use TotalRating\Entities\Entity;
use WP_Post_Type;

class PostsResolver implements EntityResolver
{

    protected $type = 'post';

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
        $post = get_post((int)$id, 'OBJECT', 'display');

        if ($post && (empty($subtype) || $post->post_type === $subtype)) {
            $url  = get_permalink($post);
            $type = $this->type . ':' . $subtype;

            $typeObject = get_post_type_object($subtype);
            $typeLabel  = ($typeObject instanceof WP_Post_Type) ? $typeObject->labels->singular_name : 'post';

            return new Entity($post->ID, $post->post_title, $type, $typeLabel, $url, $meta);
        }

        return null;
    }
}