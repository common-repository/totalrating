<?php

namespace TotalRatingVendors\TotalSuite\Foundation\WordPress\Tasks\Tracking;
! defined( 'ABSPATH' ) && exit();



use TotalRatingVendors\TotalSuite\Foundation\Task;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Plugin;

/**
 * Class Request
 *
 * @method static invoke(string $url, array $data, $method = 'POST')
 */
class SendTrackingRequest extends Task
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var
     */
    protected $data;

    /**
     * @var
     */
    protected $method = 'POST';

    /**
     * Request constructor.
     *
     * @param  string  $url
     * @param $data
     * @param $method
     */
    public function __construct(string $url, $data, $method = 'POST')
    {
        $this->url = $url;
        $this->data = $data;
        $this->method = $method;
    }


    /**
     * @return bool|mixed|void
     */
    protected function validate()
    {
        return true;
    }

    /**
     * @return mixed|void
     */
    protected function execute()
    {
        $userConsent = (bool)Plugin::options('customer.tracking', false);

        if ($userConsent) {
            wp_remote_request($this->url,
                [
                    'method'   => strtoupper($this->method),
                    'blocking' => false,
                    'body'     => [
                        'uid'     => Plugin::uid(),
                        'product' => Plugin::env('product.id'),
                        'date'    => date(DATE_ATOM),
                        'data'    => $this->data
                    ]
                ]);
        }
    }
}