<?php

namespace TotalRating\Tasks;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Task;

/**
 * Class GetExpressions
 *
 * @package TotalRating\Tasks\Options
 * @method static array invoke()
 * @method static array invokeWithFallback(array $fallback)
 */
class GetExpressions extends Task
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
            '%s vote'                 => [
                'translations' => [
                    esc_html__('%s vote', 'totalrating'),
                    esc_html__('%s votes', 'totalrating'),
                ],
            ],
            'Rated %g based on %s'    => [
                'translations' => [
                    esc_html__('Rated %g based on %s', 'totalrating'),
                ],
            ],
            'You can %s your rating.' => [
                'translations' => [
                    esc_html__('You can %s or %s your rating.', 'totalrating'),
                ],
            ],
            'or'                      => [
                'translations' => [
                    esc_html__('or', 'totalrating'),
                ],
            ],
            'Change'                  => [
                'translations' => [
                    esc_html__('Change', 'totalrating'),
                ],
            ],
            'Revoke'                  => [
                'translations' => [
                    esc_html__('Revoke', 'totalrating'),
                ],
            ],
            'Cancel'                  => [
                'translations' => [
                    esc_html__('Cancel', 'totalrating'),
                ],
            ],
            'Your comment'            => [
                'translations' => [
                    esc_html__('Your comment', 'totalrating'),
                ],
            ],
            'Comment'                 => [
                'translations' => [
                    esc_html__('Comment', 'totalrating'),
                ],
            ],
            'Submit'                  => [
                'translations' => [
                    esc_html__('Submit', 'totalrating'),
                ],
            ],
        ];
    }
}
