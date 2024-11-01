<?php


namespace TotalRating\Tasks\Options;
! defined( 'ABSPATH' ) && exit();



use TotalRating\Tasks\GetExpressions;
use TotalRatingVendors\TotalSuite\Foundation\Task;

/**
 * Class GetDefaultOptions
 *
 * @package TotalRating\Tasks\Options
 * @method static array invoke()
 * @method static array invokeWithFallback(array $fallback)
 */
class GetDefaultOptions extends Task
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
            'calculations' => [
                'roundingDecimal' => 2,
            ],
            'privacy'      => [
                'hashIP'    => false,
                'hashAgent' => false,
                'honorDNT'  => false,
            ],
            'advanced'     => [
                'lazyLoad'             => [
                    'enabled' => false,
                ],
                'proactiveCheck'       => [
                    'enabled' => false,
                ],
                'cacheCompatibility'   => true,
                'recaptcha'            => [
                    'enabled'   => false,
                    'key'       => '',
                    'secret'    => '',
                    'threshold' => 0.5,
                ],
                'metabox'              => [
                    'enabled' => true,
                ],
                'updateOriginalRating' => [
                    'enabled' => false,
                ],
            ],
            'expressions'  => array_map(
                static function ($item) {
                    $item['translations'] = [];

                    return $item;
                },
                GetExpressions::invoke()
            ),
            'general'      => [
                'showCredits' => false,
            ],
            'uninstall'    => [
                'wipeOnUninstall' => false,
            ],
        ];
    }
}
