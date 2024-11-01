<?php

namespace TotalRating\Tasks\Widget;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Models\Widget;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\DatabaseException;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Support\Arrays;
use TotalRatingVendors\TotalSuite\Foundation\Task;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Tasks\ValidateInput;

/**
 * Class UpdateWidget
 *
 * @package TotalRating\Tasks\Widget
 * @method static Widget invoke(string $widgetUid, array $data)
 * @method static Widget invokeWithFallback($fallback, string $widgetUid, array $data)
 */
class UpdateWidget extends Task
{
    /**
     * @var int
     */
    protected $widgetUid;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * constructor.
     *
     * @param       $widgetUid
     * @param array $data
     */
    public function __construct(string $widgetUid, array $data)
    {
        $this->widgetUid = $widgetUid;
        $this->data      = $data;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function validate(): bool
    {
        return ValidateInput::invoke(
            [
                'name'        => 'string|max:255',
                'title'       => 'string|max:255',
                'description' => 'string',
                'attributes'  => 'required|array',
                'settings'    => 'required|array',
            ],
            $this->data
        );
    }

    /**
     * @return Widget
     * @throws Exception
     * @throws DatabaseException
     */
    public function execute(): Widget
    {
        $widget = Widget::byUID($this->widgetUid);

        $data = Arrays::only(
            $this->data,
            [
                'name',
                'title',
                'description',
                'attributes',
                'settings',
                'enabled',
                'status',
            ]
        );

        $widget->hydrate($data, false);
        $widget->updated_at = date('Y-m-d H:i:s');

        Exception::throwUnless($widget->update(), 'Could not update the widget');

        //@TODO Check this
        //UpdateAttributes::invoke($widget, $attributes);

        return $widget;
    }
}
