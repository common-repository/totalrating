<?php

namespace TotalRating\Tasks\Rating;
! defined( 'ABSPATH' ) && exit();



use TotalRating\Models\Rating;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Task;

/**
 * Class DeleteRating
 *
 * @package TotalRating\Tasks\Rating
 * @method static Rating invoke(string $ratingUid, $emitEvent = true)
 * @method static Rating invokeWithFallback($fallback, $ratingUid, $emitEvent = true)
 */
class DeleteRating extends Task
{
    /**
     * @var string
     */
    protected $ratingUid;

    /**
     * @var bool
     */
    protected $emit;

    /**
     * constructor.
     *
     * @param string $ratingUid
     * @param bool $emitEvent
     */
    public function __construct($ratingUid, $emitEvent = true)
    {
        $this->ratingUid = $ratingUid;
        $this->emit   = $emitEvent;
    }

    /**
     * @inheritDoc
     */
    protected function validate()
    {
        return true;
    }

    /**
     * @return Rating
     * @throws Exception
     */
    protected function execute(): Rating
    {
        $rating = Rating::byUid($this->ratingUid);

        Exception::throwUnless($rating->delete(), 'Unable to delete rating, please try again.');

        return $rating;
    }
}
