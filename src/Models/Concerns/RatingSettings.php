<?php

namespace TotalRating\Models\Concerns;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Http\Response;

trait RatingSettings
{
    /**
     * @return bool
     */
    public function isAccepted(): bool
    {
        return $this->status === static::STATUS_ACCEPTED;
    }

    /**
     * @return bool
     */
    public function isChanged(): bool
    {
        return $this->status === static::STATUS_CHANGED;
    }

    /**
     * @return bool
     */
    public function isRevoked(): bool
    {
        return $this->status === static::STATUS_REVOKED;
    }
}
