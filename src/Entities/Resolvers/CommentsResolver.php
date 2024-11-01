<?php

namespace TotalRating\Entities\Resolvers;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Contracts\EntityResolver;
use TotalRating\Entities\Entity;

class CommentsResolver implements EntityResolver
{

    protected $type = 'comment';

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
        $comment = get_comment($id);

        if ($comment) {
            $url   = get_comment_link($comment);
            $title = 'comment . #' . $comment->comment_ID;

            return new Entity(
                $comment->comment_ID, $title, $this->type, esc_html__('Comment', 'totalrating'), $url, ['post' => $comment->comment_post_ID]
            );
        }

        return null;
    }
}
