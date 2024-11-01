<?php

namespace TotalRating\Tasks\Widget;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Models\Attribute;
use TotalRating\Models\Point;
use TotalRating\Models\Rating;
use TotalRating\Models\Widget;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\DatabaseException;
use TotalRatingVendors\TotalSuite\Foundation\Http\Response;
use TotalRatingVendors\TotalSuite\Foundation\Http\ResponseFactory;
use TotalRatingVendors\TotalSuite\Foundation\Support\Collection;
use TotalRatingVendors\TotalSuite\Foundation\Task;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Database\WPConnection;

/**
 * Class UpdateAttributes
 *
 * @package TotalRating\Tasks\Widget
 * @method static invoke(Widget $widget, Collection $attributes)
 */
class UpdateAttributes extends Task
{
    /**
     * @var Collection
     */
    protected $attributes;

    /**
     * @var Widget
     */
    protected $widget;

    /**
     * @var Collection
     */
    protected $queries;

    /**
     * constructor.
     *
     * @param  Widget  $widget
     * @param  Collection  $attributes
     */
    public function __construct(Widget $widget, Collection $attributes)
    {
        $this->widget     = $widget;
        $this->attributes = $attributes;
        $this->queries    = new Collection();
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
     */
    protected function execute()
    {
        /**
         * Filter scale attributes
         * Define if attributes are more or less
         * Recalculate existing ratings value
         * Deleted orphans rating if less attributes
         */
        $attributes = $this->filterAttributes();

        $response = ResponseFactory::json($this->widget);
//        $cookies  = CookieJar::createFromServer();
//        $timeout  = $this->widget->getSettings('limitations.timeout', 3600);

        /**
         * @var Attribute $attribute
         */
        foreach ($attributes as $attribute) {
            $this->processAttributes($attribute);
        }

        if ($this->queries->count()) {
            WPConnection::bulk($this->queries->flatten(), true);
        }

        return $response;
    }

    /**
     * @return array
     * @throws DatabaseException
     */
    protected function filterAttributes(): array
    {
        $attributes = [];

        /**
         * @var Attribute $attribute
         */
        foreach ($this->attributes as $attribute) {
            $exists = $this->widget->getRatingAttribute($attribute->uid);

            if ($exists instanceof Attribute) {
                if ($exists->isScale() && ($exists->getPoints()->count() !== $attribute->getPoints()->count())) {
                    $attributes[] = $attribute;
                }
            } else {
                $this->queries->add(Rating::query()->delete()->where('attribute_uid', $attribute->uid)->prepare());
            }
        }

        return $attributes;
    }

    /**
     * @param  Attribute  $attribute
     *
     * @throws DatabaseException
     */
    protected function processAttributes(Attribute $attribute)
    {
        $currentAttribute = $this->widget->getRatingAttribute($attribute->uid);

        if ($currentAttribute === null) {
            return;
        }

        $currentPoints      = $currentAttribute->getPoints()->toArray();
        $currentPointsCount = count($currentPoints);
        $currentLastPoint   = end($currentPoints);

        $previousTotalPoints = $attribute->getPoints()->count();

        /**
         * @var Point $point
         */
        foreach ($attribute->getPoints() as $point) {
            $exists = $currentAttribute->getPoint($point->uid);

            if ($exists instanceof Point) {
                $value = round(($point->value / $previousTotalPoints) * $currentPointsCount);

                $newPoint = $currentAttribute->getPoints()->where(
                    function (Point $point) use ($value) {
                        return $point->value === $value;
                    }
                );

                if ($newPoint instanceof Point) {
                    $this->queries[] = Rating::query()
                                             ->update()
                                             ->set('value', (int) $value)
                                             ->set('point_uid', $newPoint->uid)
                                             ->where('point_uid', $point->uid)
                                             ->prepare();
                }
            } else {
                $value           = $currentLastPoint->value;
                $this->queries[] = Rating::query()
                                         ->update()
                                         ->set('value', (int) $value)
                                         ->set('point_uid', $currentLastPoint->uid)
                                         ->where('point_uid', $point->uid)
                                         ->prepare();
            }
        }
    }
}
