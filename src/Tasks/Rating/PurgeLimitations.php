<?php

namespace TotalRating\Tasks\Rating;
! defined( 'ABSPATH' ) && exit();



use TotalRating\Models\Rating;
use TotalRating\Models\Widget;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\DatabaseException;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Http\CookieJar;
use TotalRatingVendors\TotalSuite\Foundation\Http\Response;
use TotalRatingVendors\TotalSuite\Foundation\Task;

/**
 * Class PurgeLimitations
 *
 * @package TotalRating\Tasks\Rating
 * @method static Response invoke(Widget $widget, Rating $rating)
 * @method static Response invokeWithFallback($fallback, Widget $widget, Rating $rating)
 */
class PurgeLimitations extends Task
{
    /**
     * @var Widget
     */
    protected $widget;

    /**
     * @var Rating
     */
    protected $rating;

    /**
     * @var Response
     */
    protected $response;

    /**
     * constructor.
     *
     * @param  Widget  $widget
     * @param  Rating  $rating
     * @param  bool  $change
     */
    public function __construct(Widget $widget, Rating $rating)
    {
        $this->widget   = $widget;
        $this->rating   = $rating;
        $this->response = $rating->toPublic()
                                 ->toJsonResponse();
    }

    /**
     * @inheritDoc
     */
    protected function validate()
    {
        return true;
    }

    /**
     * @return Response
     * @throws DatabaseException
     * @throws Exception
     */
    protected function execute(): Response
    {
        $cookies = CookieJar::instance();

        $sessionCount = (int) $cookies->get($this->rating->getRatingsCookie(), 0);
        $entityCount  = (int) $cookies->get($this->rating->getEntityCookie(), 0);

        $sessionCount = $sessionCount - 1 < 0 ? 0 : $sessionCount - 1;
        $entityCount  = $entityCount - 1 < 0 ? 0 : $entityCount - 1;

        $timeout = time() + ($this->widget->getLimitationTimeout() * 60);

        return $this->response->setCookie($this->rating->getWidgetCookie(), null, -1)
                              ->setCookie($this->rating->getAttributeCookie(), null, -1)
                              ->setCookie($this->rating->getRatingsCookie(), $sessionCount, $timeout)
                              ->setCookie($this->rating->getEntityCookie(), $entityCount, $timeout)
                              ->setCookie($this->rating->getPointCookie(), null, -1);
//
//        return $this->response->setCookie($this->rating->getWidgetCookie(), null, -1)
//                              ->setCookie($this->rating->getAttributeCookie(), null, -1)
//                              ->setCookie($this->rating->getRatingsCookie(), null, -1)
//                              ->setCookie($this->rating->getEntityCookie(), null, -1)
//                              ->setCookie($this->rating->getPointCookie(), null, -1);
    }
}
