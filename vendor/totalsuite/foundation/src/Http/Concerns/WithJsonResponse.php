<?php


namespace TotalRatingVendors\TotalSuite\Foundation\Http\Concerns;
! defined( 'ABSPATH' ) && exit();



use TotalRatingVendors\TotalSuite\Foundation\Http\Response;
use TotalRatingVendors\TotalSuite\Foundation\Http\ResponseFactory;

trait WithJsonResponse
{
    /**
     * Convert to HTTP Response.
     *
     * @return Response
     */
    public function toJsonResponse(): Response
    {
        return ResponseFactory::json($this->jsonSerialize());
    }
}