<?php

namespace TotalRating\Models;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Entities\Entity;
use TotalRating\Exceptions\WidgetNotFound;
use TotalRating\Filters\FilterWidgetUrl;
use TotalRating\Models\Concerns\WidgetSettings;
use TotalRating\Plugin;
use TotalRating\Tasks\Widget\CanViewResultsOfWidget;
use TotalRatingVendors\TotalSuite\Foundation\Database\Query\Expression;
use TotalRatingVendors\TotalSuite\Foundation\Database\TableModel;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\DatabaseException;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\NotFoundException;
use TotalRatingVendors\TotalSuite\Foundation\Support\Collection;

/**
 * Class Widget
 *
 * @property int $id
 * @property string $uid
 * @property string $title
 * @property string $name
 * @property string $description
 * @property Collection<Attribute>|Attribute[] $attributes
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property string $status
 * @property array $settings
 * @property bool $enabled
 *
 * @package TotalRating\Models
 */
class Widget extends TableModel
{
    use WidgetSettings;

    const STATUS_OPEN    = 'open';
    const STATUS_CLOSED  = 'closed';
    const STATUS_DELETED = 'deleted';
    const URL_BASE       = 'rating-widget';

    /**
     * @var string
     */
    protected $table = 'totalrating_widgets';

    /**
     * @var array
     */
    protected $types = [
        'attributes' => 'attributes',
        'settings'   => 'array',
        'enabled'    => 'bool',
    ];

    /**
     * @var array
     */
    protected $fillable = ['id', 'uid', 'user_id', 'name', 'title', 'description', 'attributes', 'status', 'settings'];

    /**
     * @param $uid
     *
     * @return Widget
     * @throws Exception
     */
    public static function byUID($uid): Widget
    {
        $widget = static::query()->where('uid', $uid)->first();

        WidgetNotFound::throwUnless((bool) $widget, 'Rating widget not found.', [], 404);

        return $widget;
    }

    /**
     * @param  mixed  $attributes
     *
     * @return Collection<Attribute>|Attribute[]
     * @noinspection PhpUnused
     */
    public function castToAttributes($attributes): Collection
    {
        $casted = [];

        $attributes = json_decode($attributes, true);

        foreach ($attributes as $attribute) {
            $casted[] = new Attribute($this, $attribute);
        }

        return Collection::create($casted);
    }

    /**
     * @param  Entity|null  $entity
     *
     * @return $this
     * @throws DatabaseException
     */
    public function withStatistics(Entity $entity = null): self
    {
        /**
         * @var Attribute $attribute
         */
        foreach ($this->getAttribute('attributes') as $attribute) {
            $attribute->withPublicStatistics($entity);
        }

        $statistics = [
            'total'   => $this->totalRatings(),
            'avg'     => $this->avgRatings(),
            'today'   => $this->todayRatings(),
            'visible' => $this->isResultsVisible(),
        ];

        $this->setAttribute('statistics', $statistics);

        return $this;
    }

    /**
     * @param  Entity|null  $entity
     *
     * @return $this
     * @throws DatabaseException
     * @throws Exception
     */
    public function withPublicStatistics(Entity $entity): self
    {
        $statistics = [
            'total'   => 0,
            'avg'     => 0,
            'visible' => false,
        ];

        if (CanViewResultsOfWidget::invoke($this, $entity)) {
            $statistics['total']   = $this->totalRatings();
            $statistics['avg']     = $this->avgRatings();
            $statistics['visible'] = true;

            /**
             * @var Attribute $attribute
             */
            foreach ($this->getAttribute('attributes') as $attribute) {
                $attribute->withPublicStatistics($entity);
            }
        }

        $this->setAttribute('statistics', $statistics);

        return $this;
    }

    /**
     * @return int
     */
    public function totalRatings(): int
    {
        return (int) Rating::query()->where('widget_uid', $this->uid)->where('status', Rating::STATUS_ACCEPTED)->count(
        );
    }

    /**
     * @return float
     */
    public function avgRatings(): float
    {
        $query = Rating::query()->avg('value', 'average')->where('widget_uid', $this->uid);
        $query = $query->where('status', Rating::STATUS_ACCEPTED)->groupBy('attribute_uid');

        $result = $query->execute();

        if (empty($result)) {
            return 0.00;
        }

        return round((float) $result[0]['average'], Plugin::options('calculations.roundingDecimal', 2));
    }

    /**
     * @return int
     */
    public function todayRatings(): int
    {
        return (int) Rating::query()->where('widget_uid', $this->uid)->where(
            new Expression('DATE(created_at)'),
            date('Y-m-d')
        )->count();
    }

    /**
     * @param  Entity  $entity
     *
     * @return Collection<Widget>
     */
    public static function byEntityAndActive(Entity $entity): Collection
    {
        /**
         * @var Collection $widgets
         */
        $widgets = self::query()->where('status', self::STATUS_OPEN)->where('enabled', true)->get();

        $widgets = $widgets->map(
            static function ($widget) use ($entity) {
                /**
                 * @var Widget $widget
                 */
                $entities = $widget->getEntities();

                if (in_array($entity->getType(), $entities, true)) {
                    return $widget;
                }

                return false;
            }
        )->filter();

        return $widgets;
    }

    /**
     * @param  int  $id
     *
     * @return Widget
     */
    public static function byIdAndActive($id)
    {
        /**
         * @var Widget $widget
         */
        $widget = self::query()->where('id', $id)->where('status', self::STATUS_OPEN)->where('enabled', true)->first();


        if (!$widget) {
            return null;
        }

        return $widget;
    }


    /**
     * @param  string  $uid
     *
     * @return Widget
     * @throws Exception
     */
    public static function byUidAndActive($uid): Widget
    {
        /**
         * @var Widget $widget
         */
        $widget = self::query()->where('uid', $uid)->where('status', self::STATUS_OPEN)->where('enabled', true)->first(
        );

        NotFoundException::throwUnless((bool) $widget, 'Widget not found');

        return $widget;
    }

    /**
     * Convert the model for public usage (hide internal attributes).
     *
     * @param  Entity|null  $entity
     *
     * @return Widget
     * @throws Exception
     */
    public function toPublic(Entity $entity = null)
    {
        $clone = clone $this;

        if ($entity) {
            try {
                $clone->withPublicStatistics($entity);
            } catch (DatabaseException $e) {
            }
            /**
             * @var Attribute $attribute
             */
            foreach ($clone->getRatingAttributes() as $attribute) {
                $attribute->withHelpers($entity);
            }
        }

        $clone->deleteAttribute('id');
        $clone->deleteAttribute('name');
        $clone->deleteAttribute('status');
        $clone->deleteAttribute('user_id');
        $clone->deleteAttribute('enabled');
        $clone->deleteAttribute('created_at');
        $clone->deleteAttribute('updated_at');
        $clone->deleteAttribute('deleted_at');
        $clone->deleteAttribute('settings.limitations');
        $clone->deleteAttribute('settings.rules');
        $clone->deleteAttribute('settings.workflow');

        return $clone;
    }

    /**
     * @param  array  $arguments
     *
     * @return string
     */
    public function getUrl($arguments = []): string
    {
        $baseUrl                 = home_url();
        $arguments['widget_uid'] = $this->uid;

        if (Plugin::env()->isPrettyPermalinks()) {
            $baseUrl = home_url(self::URL_BASE."/{$arguments['widget_uid']}/");
            unset($arguments['widget_uid']);
        }

        return FilterWidgetUrl::apply(add_query_arg($arguments, $baseUrl));
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $data        = parent::toArray();
        $data['url'] = $this->getUrl();

        return $data;
    }

    public function fromPreviewContext()
    {
        $this->setAttribute('preview', true);
        foreach ($this->getRatingAttributes() as $attribute) {
            $attribute->fromPreviewContext();
        }

        return $this;
    }

    public function isPreview()
    {
        return $this->getAttribute('preview', false) === true;
    }
}
