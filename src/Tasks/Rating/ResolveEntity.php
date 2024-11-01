<?php


namespace TotalRating\Tasks\Rating;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Entities\Entity;
use TotalRating\Entities\EntityManager;
use TotalRating\Exceptions\EntityNotFound;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Task;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Tasks\ValidateInput;

/**
 * Class ResolveEntity
 *
 * @package TotalRating\Tasks\Rating
 * @method static Entity invoke(string $id, string $type, array $meta = [])
 * @method static Entity invokeWithFallback($fallback, string $id, string $type, array $meta = [])
 */
class ResolveEntity extends Task
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $meta;

    /**
     * constructor.
     *
     * @param string $id
     * @param string $type
     * @param array $meta
     */
    public function __construct($id, $type, array $meta = [])
    {
        $this->id   = $id;
        $this->type = $type;
        $this->meta = $meta;
    }

    /**
     * @inheritDoc
     */
    protected function validate()
    {
        return ValidateInput::invoke(
            [
                'id'   => 'required|string',
                'type' => 'required|string',
                'meta' => 'array',
            ],
            [
                'id'   => $this->id,
                'type' => $this->type,
                'meta' => $this->meta,
            ]
        );
    }

    /**
     * @return Entity
     * @throws Exception
     */
    protected function execute()
    {
        $resolved = EntityManager::instance()->resolve($this->id, $this->type, $this->meta);

        EntityNotFound::throwUnless(
            $resolved,
            'Cannot resolve entity.',
            [$this->type]
        );

        return $resolved;
    }
}
