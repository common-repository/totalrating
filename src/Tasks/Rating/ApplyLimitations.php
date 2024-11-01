<?php

namespace TotalRating\Tasks\Rating;
! defined( 'ABSPATH' ) && exit();



use TotalRating\Models\Rating;
use TotalRating\Models\Widget;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Http\CookieJar;
use TotalRatingVendors\TotalSuite\Foundation\Http\Response;
use TotalRatingVendors\TotalSuite\Foundation\Task;

/**
 * Class ApplyLimitations
 *
 * @package TotalRating\Tasks\Rating
 * @method static Response invoke(Widget $widget, Rating $rating, $change = false)
 * @method static Response invokeWithFallback($fallback, Widget $widget, Rating $rating, $change = false)
 */
class ApplyLimitations extends Task
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
     * @var bool
     */
    protected $change;

    /**
     * constructor.
     *
     * @param Widget $widget
     * @param Rating $rating
     * @param bool   $change
     */
    public function __construct(Widget $widget, Rating $rating, $change = false)
    {
        $this->widget   = $widget;
        $this->rating   = $rating;
        $this->change   = $change;
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
     * @throws Exception
     */
    protected function execute(): Response
    {
        $cookies      = CookieJar::instance();
        $sessionCount = (int)$cookies->get($this->rating->getRatingsCookie(), 0);
        $entityCount  = (int)$cookies->get($this->rating->getEntityCookie(), 0);
        $timeout      = time() + ($this->widget->getLimitationTimeout() * 60);

        $this->response->setCookie($this->rating->getWidgetCookie(), true, $timeout);

        if ($this->change) {
            return $this->response->setCookie($this->rating->getAttributeCookie(), $this->rating->uid, $timeout)
                                  ->setCookie($this->rating->getPointCookie(), $this->rating->point_uid, $timeout);
        }

        return $this->response->setCookie($this->rating->getWidgetCookie(), $this->rating->widget_uid, $timeout)
                              ->setCookie($this->rating->getAttributeCookie(), $this->rating->uid, $timeout)
                              ->setCookie($this->rating->getRatingsCookie(), $sessionCount + 1, $timeout)
                              ->setCookie($this->rating->getEntityCookie(), $entityCount + 1, $timeout)
                              ->setCookie($this->rating->getPointCookie(), $this->rating->point_uid, $timeout);
    }
}
