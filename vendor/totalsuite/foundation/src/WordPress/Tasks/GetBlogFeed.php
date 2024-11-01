<?php

namespace TotalRatingVendors\TotalSuite\Foundation\WordPress\Tasks;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Support\Collection;
use TotalRatingVendors\TotalSuite\Foundation\Task;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Plugin;

/**
 * Class BlogFeed
 * @package TotalRatingVendors\TotalSuite\Foundation\WordPress\Tasks\Dashboard
 * @method static Collection invoke()
 * @method static Collection invokeWithFallback($fallback = false)
 */
class GetBlogFeed extends Task
{
    /**
     * @inheritDoc
     */
    protected function validate()
    {
        return true;
    }

    /**
     * @return Collection
     */
    protected function execute()
    {
        $url = Plugin::env('url.blogFeed');

        // Retrieve from cache first
        $cacheKey = md5($url);
        if ($cached = get_transient($cacheKey)):
            return Collection::create($cached);
        endif;

        // Fetch
        $request = wp_remote_get($url);

        // Decode response
        $response = json_decode(wp_remote_retrieve_body($request), true) ?: [];
        $blogPosts = [];

        if (!empty($response)):
            $blogPosts = $response;

            // Cache
            set_transient($cacheKey, $blogPosts, DAY_IN_SECONDS * 2);
        endif;

        return Collection::create($blogPosts);
    }
}
