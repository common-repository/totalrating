<?php


namespace TotalRatingVendors\TotalSuite\Foundation\Http\Concerns;
! defined( 'ABSPATH' ) && exit();



trait Shorthands
{
    /**
     * Get IP.
     *
     * @return string
     */
    public function ip()
    {
        return $this->getServerParam('REMOTE_ADDR');
    }

    /**
     * Get User agent.
     *
     * @return string
     */
    public function userAgent()
    {
        return $this->getServerParam('HTTP_USER_AGENT');
    }
}