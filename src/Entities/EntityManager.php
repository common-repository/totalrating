<?php

namespace TotalRating\Entities;
! defined( 'ABSPATH' ) && exit();


use InvalidArgumentException;
use TotalRating\Contracts\EntityResolver;
use TotalRatingVendors\TotalSuite\Foundation\Helpers\Concerns\ResolveFromContainer;

class EntityManager
{
    use ResolveFromContainer;

    /**
     * @var EntityResolver[]
     */
    protected $resolvers = [];

    /**
     * @param EntityResolver $resolver
     */
    public function registerResolver(EntityResolver $resolver)
    {
        if (array_key_exists($resolver->getType(), $this->resolvers)) {
            throw new InvalidArgumentException(
                sprintf(
                    'A Resolver with the same name (%s) is already registered',
                    $resolver->getType()
                )
            );
        }

        $this->resolvers[$resolver->getType()] = $resolver;
    }

    /**
     * @param null $widgetUid
     *
     * @return Entity
     */
    public function resolveFromContext($widgetUid = null)
    {
        if (is_singular() || in_the_loop()) {
            $type = 'post:' . strtolower(get_post_type());
            $id   = get_the_ID();

            return $this->resolve($id, $type, []);
        }

        if (is_tax()) {
            $type       = 'taxonomy';
            $id         = get_queried_object_id();

            return $this->resolve($id, $type);
        }

        if ($widgetUid !== null) {
            return $this->resolve($widgetUid, 'widget');
        }

        return null;
    }

    /**
     * @param       $id
     * @param       $type
     * @param array $meta
     *
     * @return Entity
     */
    public function resolve($id, $type, array $meta = [])
    {
        $type = explode(':', $type);

        if (!empty($type) && $resolver = $this->getResolver($type[0])) {
            return $resolver->resolve($id, $type[1] ?? $type[0] ?? null, $meta ?: []);
        }

        return null;
    }

    /**
     * @param $type
     *
     * @return bool|EntityResolver
     */
    public function getResolver($type)
    {
        if ($this->hasResolver($type)) {
            return $this->resolvers[$type];
        }

        return false;
    }

    /**
     * @param $type
     *
     * @return bool
     */
    public function hasResolver($type): bool
    {
        return array_key_exists($type, $this->resolvers);
    }
}
