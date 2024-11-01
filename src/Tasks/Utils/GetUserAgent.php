<?php


namespace TotalRating\Tasks\Utils;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Plugin;
use TotalRatingVendors\TotalSuite\Foundation\Task;

/**
 * Class ApplyPrivacyOptions
 *
 * @package TotalRating\Tasks
 * @method static string invoke()
 * @method static string invokeWithFallback()
 */
class GetUserAgent extends Task
{
    protected function validate()
    {
        return true;
    }

    protected function execute()
    {
        $request  = Plugin::request();
        $honorDNT = Plugin::options('privacy.honorDNT', false) && $request->hasHeader('dnt');

        if ($honorDNT || Plugin::options('privacy.hashAgent', false)) {
            return sha1($request->userAgent());
        }

        return $request->userAgent();
    }
}
