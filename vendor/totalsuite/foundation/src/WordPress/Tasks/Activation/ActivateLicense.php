<?php

namespace TotalRatingVendors\TotalSuite\Foundation\WordPress\Tasks\Activation;
! defined( 'ABSPATH' ) && exit();



use TotalRatingVendors\TotalSuite\Foundation\Exceptions\ActivationException;
use TotalRatingVendors\TotalSuite\Foundation\License;
use TotalRatingVendors\TotalSuite\Foundation\Task;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Plugin;

/**
 * Class ActivateLicense
 * @package TotalSurveyVendors\TotalRatingVendors\TotalSuite\Foundation\WordPress\Tasks\Activation
 *
 * @method static License invoke(string $key, string $domain, string $product)
 * @method static License invokeWithFallback($fallback, string $key, string $domain, string $product)
 */
class ActivateLicense extends Task
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var string
     */
    protected $product;

    /**
     * ActivateLicense constructor.
     *
     * @param string $key
     * @param string $domain
     * @param string $product
     */
    public function __construct(string $key, string $domain, string $product)
    {
        $this->key = $key;
        $this->domain = $domain;
        $this->product = $product;
    }


    protected function validate()
    {
        return true;
    }

    /**
     * @return mixed
     * @throws ActivationException
     */
    protected function execute()
    {
        $url = Plugin::env('url.activation.activate', 'https://totalsuite.net/api/v3/activate');

        $url = add_query_arg([
            'license' => $this->key,
            'domain' => $this->domain,
            'from_product' => $this->product
        ], $url);

        $response = wp_remote_get($url);

        if (is_wp_error($response)) {
            throw new ActivationException(__('Activation error.', Plugin::env('textdomain')), $response->errors);
        }

        $apiResponse = json_decode(wp_remote_retrieve_body($response), true);

        if (empty($apiResponse['success'])) {
            throw new ActivationException($apiResponse['message'] ?? __('Activation error.', Plugin::env('textdomain')));
        }

        return License::persist($apiResponse['data'] ?? []);
    }
}