<?php

namespace TotalRating\Admin;
! defined( 'ABSPATH' ) && exit();



use TotalRating\Admin\Concerns\FilterQuery;
use TotalRating\Entities\Entity;
use TotalRating\Entities\EntityManager;
use TotalRating\Models\Attribute;
use TotalRating\Models\Widget;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Helpers\Concerns\ResolveFromContainer;
use TotalRatingVendors\TotalSuite\Foundation\Helpers\Html;
use TotalRatingVendors\TotalSuite\Foundation\Http\Request;
use TotalRatingVendors\TotalSuite\Foundation\Support\Collection;

class EntityFilter
{
    use FilterQuery;
    use ResolveFromContainer;

    const COLUMN_NAME = 'totalrating_rating_column';
    const QUERY_PARAM = 'totalrating_attribute';

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Widget|Collection
     */
    protected $widgets;

    /**
     * @var Attribute
     */
    protected $currentAttribute;

    /**
     * @var array
     */
    protected $params = [
        'value'     => '',
        'attribute' => '',
        'type'      => '',
        'direction' => 'ASC',
    ];

    /**
     * EntityFilter constructor.
     *
     * @param  Request  $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;

        add_action('pre_get_posts', [$this, 'initialize']);
    }

    public function initialize()
    {
        global $post_type;

        if ($post_type) {
            if(is_array($post_type)){
                $post_type = current($post_type);
            }

            $this->widgets = $this->getWidgetFromPostType($post_type);

            if (!$this->widgets->isEmpty()) {
                $this->parseQueryParam();

                add_action('restrict_manage_posts', [$this, 'displayFilters']);

                if ($this->params['attribute'] && $this->setCurrentAttribute()) {
                    add_filter('posts_join_paged', [$this, 'filterJoin'], 10, 2);
                    add_filter('posts_fields', [$this, 'filterFields'], 10, 2);
                    add_filter('posts_orderby', [$this, 'filterOrder'], 10, 2);
                    add_filter('posts_groupby', [$this, 'filterGroup'], 10, 2);
                    add_filter('posts_distinct', [$this, 'filterDistinct'], 10, 2);

                    add_filter("manage_{$post_type}_posts_columns", [$this, 'displayColumnHeader'], 10, 2);
                    add_action("manage_{$post_type}_posts_custom_column", [$this, 'displayColumnValue'], 10, 2);
                }
            }
        }
    }

    /**
     * @param  array  $columns
     *
     * @return array
     */
    public function displayColumnHeader(array $columns)
    {
        if ($this->currentAttribute->type === Attribute::TYPE_SCALE) {
            $columns[self::COLUMN_NAME] = esc_html__('Avg. rating', 'totalrating');
        } else {
            $columns[self::COLUMN_NAME] = esc_html__('Points', 'totalrating');
        }

        return $columns;
    }

    /**
     * @param $column
     * @param $post_id
     */
    public function displayColumnValue($column, $post_id)
    {
        global $post_type;

        if ($column === self::COLUMN_NAME) {
            if ($this->currentAttribute instanceof Attribute) {
                try {
                    $entity = EntityManager::instance()->resolve($post_id, 'post:'.$post_type);
                    $this->currentAttribute->withPublicStatistics($entity);

                    echo esc_html($this->currentAttribute->getAttribute('statistics.text'));
                } catch (Exception $exception) {
                    echo '';
                }
            }
        }
    }

    public function displayFilters()
    {
        global $pagenow, $post_type;

        if ($this->widgets->isEmpty()) {
            return;
        }

        $select = Html::create(
            'select',
            [
                'name'     => self::QUERY_PARAM,
                'onchange' => 'javascript:document.querySelector("#posts-filter").submit()',
            ]
        );

        $select->addContent(
            Html::create(
                'option',
                ['value' => ''],
                esc_html__('Sort by ratings', 'totalrating')
            )
        );

        /**
         * @var Widget $widget
         */
        foreach ($this->widgets as $widget):

            $group = Html::create(
                'optgroup',
                [
                    'label' => $widget->name ?: "Widget #$widget->id",
                ]
            );

            /**
             * @var Attribute $attribute
             */
            foreach ($widget->getRatingAttributes() as $attribute) {
                $group->addContent(
                    [
                        $this->createOption($attribute, 'desc'),
                        $this->createOption($attribute),
                    ]
                );
            }

            $select->addContent($group);
        endforeach;

        $reset = '';

        if ($this->params['value']) {
            $reset = Html::create(
                'a',
                [
                    'href'  => add_query_arg('post_type', $post_type, admin_url($pagenow)),
                    'class' => 'button',
                    'style' => 'margin-right: 1rem',
                ],
                esc_html__('Reset sorting', 'totalrating')
            );
        }

        echo wp_kses($select.$reset, [
            'a'        => ['href' => [], 'class' => [], 'style' => []],
            'select'   => ['name' => [], 'onchange' => []],
            'option'   => ['value' => []],
            'optgroup' => ['label' => []],
        ]);
    }

    /**
     * @return bool
     */
    protected function setCurrentAttribute()
    {
        $uid = $this->params['attribute'];

        /**
         * @var Widget $widget
         */
        foreach ($this->widgets as $widget) {
            $attribute = $widget->getRatingAttribute($uid);

            if ($attribute instanceof Attribute) {
                $this->currentAttribute = $attribute;

                return true;
            }
        }

        return false;
    }

    /**
     * @param  Attribute  $attribute
     * @param  string  $direction
     *
     * @return Html
     */
    protected function createOption(Attribute $attribute, $direction = 'asc')
    {
        if ($attribute->type === Attribute::TYPE_SCALE) {
            $label = $attribute->label.' '.esc_html__('Average', 'totalrating');
        } else {
            $label = $attribute->label.' ';
        }

        $value = $attribute->uid.'_'.$attribute->type.'_'.$direction;

        $optionAttributes = ['value' => $value];
        if ($value == $this->params['value']) {
            $optionAttributes['selected'] = true;
        }

        return Html::create(
            'option',
            $optionAttributes,
            sprintf(esc_html__('Sort by %s (%s)', 'totalrating'), $label, strtoupper($direction))
        );
    }

    /**
     * @param  string  $postType
     *
     * @return Collection
     */
    protected function getWidgetFromPostType($postType)
    {
        $entity = new Entity(0, '', "post:$postType", $postType, '');

        return Widget::byEntityAndActive($entity);
    }

    protected function parseQueryParam()
    {
        $filter = $this->request->getQueryParam(self::QUERY_PARAM, '');

        if (empty($filter)) {
            return;
        }

        $query = explode('_', $filter, 3);

        array_unshift($query, $filter);

        $params = array_combine(['value', 'attribute', 'type', 'direction'], array_values($query));

        $this->params = array_merge($this->params, $params);
    }

}
