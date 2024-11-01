<?php

namespace TotalRating\Tasks\Widget;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Models\Widget;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Support\Arrays;
use TotalRatingVendors\TotalSuite\Foundation\Support\Strings;
use TotalRatingVendors\TotalSuite\Foundation\Task;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Tasks\ValidateInput;

/**
 * Class CreateWidget
 *
 * @package TotalRating\Tasks\Widget
 * @method static Widget invoke(array $data)
 * @method static Widget invokeWithFallback($fallback, array $data)
 */
class CreateWidget extends Task
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        return ValidateInput::invoke(
            [
                'name'        => 'string|max:255',
                'title'       => 'string|max:255',
                'description' => 'string|min:6',
                'attributes'  => 'array',
                'settings'    => 'array',
            ],
            $this->data
        );
    }

    /**
     * @return Widget
     * @throws Exception
     */
    public function execute()
    {
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

        $data['uid']     = Strings::uid();
        $data['user_id'] = get_current_user_id();
        $data['status']  = Widget::STATUS_OPEN;
        $widget          = Widget::create($data);

        Exception::throwUnless($widget, 'Could not create the widget');

        return $widget;
    }
}
