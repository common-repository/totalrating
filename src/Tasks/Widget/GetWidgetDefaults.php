<?php

namespace TotalRating\Tasks\Widget;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Support\Collection;
use TotalRatingVendors\TotalSuite\Foundation\Task;

/**
 * Class GetWidgetDefaults
 *
 * @package TotalRating\Tasks\Widget
 * @method static Collection invoke()
 * @method static Collection invokeWithFallback($fallback)
 */
class GetWidgetDefaults extends Task
{

    protected function validate()
    {
        return true;
    }

    protected function execute(): Collection
    {
        return Collection::create(
            [
                'name'        => '',
                'title'       => '',
                'description' => '',
                'attributes'  => [],
                'settings'    => [
                    'limitations' => [
                        'session' => 1,
                        'entity'  => 1,
                        'timeout' => 60,
                        'cookies' => ['enabled' => true],
                        'ip'      => ['enabled' => false],
                    ],
                    'rules'       => [
                        'entities' => ['post:post'],
                    ],
                    'design'      => [
                        'colors'      => [
                            'primary'    => [
                                'base'     => '#0288D1',
                                'contrast' => '#FFFFFF',
                            ],
                            'secondary'  => [
                                'base'     => '#DDDDDD',
                                'contrast' => '#FFFFFF',
                            ],
                            'background' => [
                                'base'     => '#f2f2f2',
                                'contrast' => '#FFFFFF',
                            ],
                            'dark'       => [
                                'base'     => '#666666',
                                'contrast' => '#FFFFFF',
                            ],
                        ],
                        'scheme'      => 'light',
                        'size'        => 'regular',
                        'space'       => 'normal',
                        'emplacement' => 'after_content',
                        'position'    => 'above',
                        'radius'      => 'sharp',
                    ],
                    'behaviours'  => [
                        'autoIntegrate' => ['enabled' => true],
                        'socialSharing' => ['enabled' => true],
                        'hideResults'   => ['enabled' => false, 'for' => 'all'],
                        'recaptcha'     => ['enabled' => false],
                        'confirmation'  => ['enabled' => false],
                        'change'        => ['enabled' => false],
                        'revoke'        => ['enabled' => false],
                    ],
                    'workflow'    => [
                        'rules' => [],
                    ],
                ],
            ]
        );
    }
}
