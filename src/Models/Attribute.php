<?php

namespace TotalRating\Models;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Entities\Entity;
use TotalRating\Filters\FilterAttributeStatistics;
use TotalRating\Plugin;
use TotalRating\Tasks\Rating\CanChange;
use TotalRating\Tasks\Rating\CanRate;
use TotalRating\Tasks\Rating\CanRevoke;
use TotalRating\Tasks\Utils\GetIP;
use TotalRating\Tasks\Utils\GetUserAgent;
use TotalRating\Tasks\Widget\CanViewResultsOfAttribute;
use TotalRatingVendors\TotalSuite\Foundation\Database\Model;
use TotalRatingVendors\TotalSuite\Foundation\Database\Query\Expression;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\DatabaseException;
use TotalRatingVendors\TotalSuite\Foundation\Http\CookieJar;
use TotalRatingVendors\TotalSuite\Foundation\Support\Collection;
use TotalRatingVendors\TotalSuite\Foundation\Support\Strings;

/**
 * Class Attribute
 *
 * @package TotalRating\Models
 *
 * @property string $uid
 * @property string $label
 * @property string $type
 * @property array $point
 * @property array $statistics
 * @property string $rating
 * @property string $checked
 * @property boolean $canRate
 * @property boolean $canChange
 * @property boolean $canRevoke
 */
class Attribute extends Model
{
    const TYPE_COUNT = 'count';
    const TYPE_SCALE = 'scale';

    /**
     * @var array
     */
    protected $types = [
        'points' => 'points',
    ];

    /**
     * @var array
     */
    protected $statistics = [
        'total'     => 0,
        'avg'       => 0.0,
        'entity'    => 0.0,
        'scale'     => 0,
        'text'      => '',
        'fragments' => [
            'votes' => '',
            'rate'  => '',
            'rated' => '',
            'based' => '',
        ],
        'visible'   => false,
    ];

    /**
     * @var Widget
     */
    protected $widget;

    /**
     * constructor.
     *
     * @param  Widget  $widget
     * @param  array  $attributes
     */
    public function __construct(Widget $widget, $attributes = [])
    {
        $this->widget             = $widget;
        $attributes['statistics'] = $this->statistics;

        parent::__construct($attributes);
    }

    /**
     * @param  mixed  $attributes
     *
     * @return Collection
     * @noinspection PhpUnused
     */
    public function castToPoints($attributes): Collection
    {
        $casted = [];

        foreach ($attributes as $index => $attribute) {
            $attribute['value'] = $index + 1;
            $casted[]           = new Point($this, $attribute);
        }

        return Collection::create($casted);
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->getAttribute('label');
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->getAttribute('type');
    }

    /**
     * @return string
     */
    public function getResultsFormat(): string
    {
        return $this->getAttribute('resultsFormat', '{{rated}}');
    }

    /**
     * @param  Entity  $entity
     *
     * @return int
     * @throws DatabaseException
     */
    public function total(Entity $entity = null): int
    {
        $total = 0;

        $query = Rating::query()
                       ->column(new Expression('COUNT(*) AS total'))
                       ->column('point_uid')
                       ->where('widget_uid', $this->widget->uid)
                       ->where('attribute_uid', $this->getUid())
                       ->where('status', Rating::STATUS_ACCEPTED);

        if ($entity !== null) {
            $query->where('entity_id', $entity->getId())
                  ->where('entity_type', $entity->getType());
        }

        $ratings = $query->groupBy('point_uid')->all();

        foreach ($ratings as $rating) {
            $point = $this->getPoint($rating['point_uid']);

            if ($point instanceof Point) {
                $point->total = $rating['total'];
                $total        += $rating['total'];
            }
        }

        return (int) $total;
    }

    /**
     * @return string
     */
    public function getUid(): string
    {
        return $this->getAttribute('uid');
    }

    /**
     * @param  string  $pointUid
     *
     * @return Point|null
     */
    public function getPoint($pointUid)
    {
        return $this->getPoints()->first(
            static function (Point $point) use ($pointUid) {
                return $point->uid === $pointUid;
            }
        );
    }

    /**
     * @return Collection
     */
    public function getPoints(): Collection
    {
        return $this->getAttribute('points', []);
    }

    /**
     * @param  Entity  $entity
     *
     * @return float
     * @throws DatabaseException
     */
    public function avg(Entity $entity = null): float
    {
        if ($this->getAttribute('type') === static::TYPE_COUNT) {
            return 0;
        }

        $query = Rating::query()->avg('value', 'average')
                       ->where('widget_uid', $this->widget->uid)
                       ->where('attribute_uid', $this->getUid())
                       ->where('status', Rating::STATUS_ACCEPTED);

        if ($entity !== null) {
            $query->where('entity_id', $entity->getId())
                  ->where('entity_type', $entity->getType());
        }

        $result = $query->execute();

        if (empty($result) || (isset($result[0]['average']) && $result[0]['average'] === null)) {
            return 0.0;
        }

        return round((float) $result[0]['average'], Plugin::options('calculations.roundingDecimal', 2));
    }

    /**
     * @param  Entity  $entity
     *
     * @return $this
     * @throws DatabaseException
     * @noinspection PhpUnused
     */
    public function withStatistics(Entity $entity = null): self
    {
        $this->statistics['visible'] = true;
        $this->statistics['total']   = $this->total($entity);
        $this->statistics['avg']     = $this->avg($entity);
        $this->statistics['scale']   = count($this->getPoints());

        $vars                      = [];
        $vars['votes']             = $this->statistics['total'];
        $vars['votesStd']          = number_format_i18n($this->statistics['total']);
        $vars['votesStdWithLabel'] = Strings::template(
            _n(
                '{{votesStd}} vote',
                '{{votesStd}} votes',
                $this->statistics['total'],
                'totalrating'
            ),
            $vars
        );
        $vars['avg']               = number_format_i18n(
            $this->statistics['avg'],
            Plugin::options('calculations.roundingDecimal', 2)
        );;
        $vars['avgStd'] = sprintf('%g', $vars['avg']);
        $vars['scale']  = $this->statistics['scale'];
        $vars['rate']   = Strings::template(esc_html__('{{avgStd}} out of {{scale}}', 'totalrating'), $vars);

        $this->statistics['fragments']['rate']  = $vars['rate'];
        $this->statistics['fragments']['votes'] = $vars['votesStdWithLabel'];
        $this->statistics['fragments']['rated'] = Strings::template(esc_html__('Rated {{rate}}', 'totalrating'), $vars);
        $this->statistics['fragments']['based'] = Strings::template(
            esc_html__('based on {{votesStdWithLabel}}', 'totalrating'),
            $vars
        );

        $this->statistics['text'] = Strings::template(
            $this->isScale() ? $this->getResultsFormat() : esc_html__('{{votes}}', 'totalrating'),
            $this->statistics['fragments']
        );

        $this->setAttribute('statistics', FilterAttributeStatistics::apply($this->statistics, $this));

        return $this;
    }

    /**
     * @param  Entity|null  $entity
     *
     * @return Attribute
     * @throws DatabaseException
     */
    public function withPublicStatistics(Entity $entity = null): self
    {
        if (CanViewResultsOfAttribute::invokeWithFallback(false, $this->widget, $this, $entity)) {
            $this->withStatistics($entity);
        }

        $value = floor($this->statistics['avg']);

        $point = $this->getPoints()->first(
            static function (Point $point) use ($value) {
                return $point->value === $value;
            }
        );

        if ($point instanceof Point) {
            $this->rating  = true;
            $this->checked = $point->uid;
        }

        return $this;
    }

    static protected $cachedHelpersOfEntity = [];

    /**
     * @param  Entity  $entity
     *
     * @return Attribute
     * @throws \TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception
     */
    public function withHelpers(Entity $entity): self
    {
        $cacheUid = "{$this->uid}:{$entity->getUid()}";

        // @TODO Improve this by updating the logic to make it less computationally when called more than once
        if (isset(static::$cachedHelpersOfEntity[$cacheUid])) {
            return static::$cachedHelpersOfEntity[$cacheUid];
        }

        if ($this->widget->isPreview()) {
            return $this->fromPreviewContext();
        }

        // Helpers
        if ($this->widget->isLimitationByCookiesEnabled()) {
            $cookies       = CookieJar::instance();
            $this->rating  = $cookies->get(
                Rating::getCookieName('widget', $this->uid, $entity->getType(), $entity->getId()),
                ''
            );
            $this->checked = $cookies->get(
                Rating::getCookieName('point', $this->uid, $entity->getType(), $entity->getId()),
                ''
            );
        }

        if ($this->checked) {
            $this->canRate = CanRate::invokeWithFallback(
                false,
                $this->widget,
                $entity,
                [
                    'attribute_uid' => $this->uid,
                    'ip'            => GetIP::invoke(),
                    'agent'         => GetUserAgent::invoke(),
                ]
            );
            if ($this->canRate) {
                $this->rating  = '';
                $this->checked = '';
            }
            $this->canChange = CanChange::invokeWithFallback(false, $this->widget);
            $this->canRevoke = CanRevoke::invokeWithFallback(false, $this->widget);
        } else {
            $this->canRate   = CanRate::invokeWithFallback(
                false,
                $this->widget,
                $entity,
                [
                    'attribute_uid' => $this->uid,
                    'ip'            => GetIP::invoke(),
                    'agent'         => GetUserAgent::invoke(),
                ]
            );
            $this->canChange = $this->widget->isChangeAllowed();
            $this->canRevoke = $this->widget->isRevokeAllowed();
        }

        // @TODO Improve this, make it side-effect-less
        static::$cachedHelpersOfEntity[$cacheUid] = clone $this;

        return $this;
    }

    /**
     * @return $this
     */
    public function fromRevokeContext(): Attribute
    {
        $this->canRate   = true;
        $this->canChange = false;
        $this->canRevoke = false;

        return $this;
    }

    /**
     * @return $this
     */
    public function fromPreviewContext(): Attribute
    {
        $this->canRate   = true;
        $this->canChange = true;
        $this->canRevoke = true;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $attributes = parent::toArray();
        unset($attributes['widget']);

        return $attributes;
    }

    public function getStatistics(): array
    {
        return $this->statistics;
    }

    /**
     * @return bool
     */
    public function isCount(): bool
    {
        return $this->getAttribute('type') === static::TYPE_COUNT;
    }

    /**
     * @return bool
     */
    public function isScale(): bool
    {
        return $this->getAttribute('type') === static::TYPE_SCALE;
    }
}
