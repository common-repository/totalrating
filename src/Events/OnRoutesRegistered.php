<?php
namespace TotalRating\Events;
! defined( 'ABSPATH' ) && exit();



use TotalRatingVendors\TotalSuite\Foundation\Event;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Rest\Router;

class OnRoutesRegistered extends Event
{
    /**
     * @var Router
     */
    protected $router;

    /**
     * constructor.
     *
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @return Router
     */
    public function getRouter(): Router
    {
        return $this->router;
    }
}