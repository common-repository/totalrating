<?php

namespace TotalRating\Entities\Resolvers;
! defined( 'ABSPATH' ) && exit();



use TotalRating\Contracts\EntityResolver;
use TotalRating\Entities\Entity;

class UsersResolver implements EntityResolver
{

    protected $type = 'user';

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
        $user = get_user_by('ID', (int)$id);

        if ($user) {
            return new Entity(
                $user->ID,
                $user->display_name,
                $this->type,
                get_role($user->roles[0])->name,
                get_author_posts_url($user->ID)
            );
        }

        return null;
    }
}
