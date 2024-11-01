<?php


namespace TotalRating\Tasks\Widget;
! defined( 'ABSPATH' ) && exit();



use TotalRating\Entities\Entity;
use TotalRating\Entities\EntityManager;
use TotalRatingVendors\TotalSuite\Foundation\Task;

/**
 * Class GetContext
 *
 * @package TotalRating\Tasks\Widget
 * @method static Entity|null invoke(EntityManager $resolver)
 * @method static Entity|null invokeWithFallback($fallback, EntityManager $resolver)
 */
class GetContext extends Task
{
    /**
     * @var EntityManager
     */
    protected $resolver;

    /**
     * constructor.
     *
     * @param EntityManager $resolver
     */
    public function __construct(EntityManager $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @inheritDoc
     */
    protected function validate()
    {
        return true;
    }

    /**
     * @return Entity|null
     */
    protected function execute()
    {
        return $this->resolver->resolveFromContext();
    }
}
