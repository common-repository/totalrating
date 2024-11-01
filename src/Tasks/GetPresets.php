<?php

namespace TotalRating\Tasks;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Support\Strings;
use TotalRatingVendors\TotalSuite\Foundation\Task;

/**
 * Class GetPresets
 *
 * @package TotalRating\Tasks\Options
 * @method static array invoke()
 * @method static array invokeWithFallback(array $fallback)
 */
class GetPresets extends Task
{
    /**
     * @inheritDoc
     */
    protected function validate()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function execute()
    {
        return [
            [
                'id'    => 'faces',
                'icon'  => 'mood',
                'label' => 'Faces',
                'data'  => [
                    [
                        'uid'    => Strings::uid(),
                        'type'   => 'count',
                        'label'  => 'Happiness',
                        'points' => [
                            [
                                'uid'    => Strings::uid(),
                                'symbol' => [
                                    'type'    => 'text',
                                    'default' => '😞',
                                    'active'  => '😞',
                                    'filter'  => 'grayscale',
                                ],
                                'label'  => 'Unhappy',
                                'value'  => 1,
                            ],
                            [
                                'uid'    => Strings::uid(),
                                'symbol' => [
                                    'type'    => 'text',
                                    'default' => '😐',
                                    'active'  => '😐',
                                    'filter'  => 'grayscale',
                                ],
                                'label'  => 'Neutral',
                                'value'  => 2,
                            ],
                            [
                                'uid'    => Strings::uid(),
                                'symbol' => [
                                    'type'    => 'text',
                                    'default' => '🙂',
                                    'active'  => '🙂',
                                    'filter'  => 'grayscale',
                                ],
                                'label'  => 'Happy',
                                'value'  => 3,
                            ],
                            [
                                'uid'    => Strings::uid(),
                                'symbol' => [
                                    'type'    => 'text',
                                    'default' => '😀',
                                    'active'  => '😀',
                                    'filter'  => 'grayscale',
                                ],
                                'label'  => 'Enjoyed',
                                'value'  => 4,
                            ],
                        ],
                    ],
                ],
                'meta'  => ['visible' => 'all'],
            ],
            [
                'id'    => 'fivestars',
                'icon'  => 'star',
                'label' => '5 Stars',
                'data'  => [
                    [
                        'uid'    => Strings::uid(),
                        'type'   => 'scale',
                        'label'  => 'Rating',
                        'points' => [
                            [
                                'uid'    => Strings::uid(),
                                'symbol' => [
                                    'type'    => 'text',
                                    'default' => '⭐',
                                    'active'  => '⭐',
                                    'filter'  => null,
                                ],
                                'label'  => '1 Star',
                                'value'  => 1,
                            ],
                            [
                                'uid'    => Strings::uid(),
                                'symbol' => [
                                    'type'    => 'text',
                                    'default' => '⭐',
                                    'active'  => '⭐',
                                    'filter'  => null,
                                ],
                                'label'  => '2 Stars',
                                'value'  => 2,
                            ],
                            [
                                'uid'    => Strings::uid(),
                                'symbol' => [
                                    'type'    => 'text',
                                    'default' => '⭐',
                                    'active'  => '⭐',
                                    'filter'  => null,
                                ],
                                'label'  => '3 Stars',
                                'value'  => 3,
                            ],
                            [
                                'uid'    => Strings::uid(),
                                'symbol' => [
                                    'type'    => 'text',
                                    'default' => '⭐',
                                    'active'  => '⭐',
                                    'filter'  => null,
                                ],
                                'label'  => '4 Stars',
                                'value'  => 4,
                            ],
                            [
                                'uid'    => Strings::uid(),
                                'symbol' => [
                                    'type'    => 'text',
                                    'default' => '⭐',
                                    'active'  => '⭐',
                                    'filter'  => null,
                                ],
                                'label'  => '5 Stars',
                                'value'  => 5,
                            ],
                        ],
                    ],
                ],
                'meta'  => ['visible' => 'all'],
            ],
            [
                'id'    => 'thumbs',
                'icon'  => 'thumbs_up_down',
                'label' => 'Thumbs',
                'data'  => [
                    [
                        'uid'    => Strings::uid(),
                        'type'   => 'count',
                        'label'  => 'Like',
                        'points' => [
                            [
                                'uid'    => Strings::uid(),
                                'symbol' => [
                                    'type'    => 'text',
                                    'default' => '👎🏻',
                                    'active'  => '👎',
                                    'filter'  => 'grayscale',
                                ],
                                'label'  => 'Thumbs down',
                                'value'  => 1,
                            ],
                            [
                                'uid'    => Strings::uid(),
                                'symbol' => [
                                    'type'    => 'text',
                                    'default' => '👍🏻',
                                    'active'  => '👍',
                                    'filter'  => 'grayscale',
                                ],
                                'label'  => 'Thumbs up',
                                'value'  => 2,
                            ],
                        ],
                    ],
                ],
                'meta'  => ['visible' => 'all'],
            ],
            [
                'id'    => 'empty',
                'icon'  => 'web_asset',
                'label' => 'Empty',
                'data'  => [],
                'meta'  => ['visible' => 'wizard'],
            ],
        ];
    }
}
