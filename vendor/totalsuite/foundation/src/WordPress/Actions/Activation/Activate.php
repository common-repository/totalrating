<?php

namespace TotalRatingVendors\TotalSuite\Foundation\WordPress\Actions\Activation;
! defined( 'ABSPATH' ) && exit();



use TotalRatingVendors\TotalSuite\Foundation\Action;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Http\ResponseFactory;
use TotalRatingVendors\TotalSuite\Foundation\License;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Plugin;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Tasks\Activation\ActivateLicense;


/**
 * Class Activate
 *
 * @package TotalSurveyVendors\TotalRatingVendors\TotalSuite\Foundation\WordPress\Actions\Activation
 */
class Activate extends Action
{
    /**
     * @var License
     */
    protected $license;

    /**
     * Create constructor.
     *
     * @param License $license
     */
    public function __construct(License $license)
    {
        $this->license = $license;
    }


    /**
     * @throws Exception
     */
    protected function execute()
    {
        if ($this->license->isRegistered()) {
            return ResponseFactory::json([
                'message' => __('You have already activated your copy.'),
                'license' => $this->license
            ]);
        }

        $key = (string)$this->request->getParsedBodyParam('key');
        $host = Plugin::env()->hostName();
        $product = Plugin::env()->get('product.id');

        try {
            $license = ActivateLicense::invoke($key, $host, $product);

            return ResponseFactory::json(
                [
                    'message' => __('You have successfully activated your copy.'),
                    'license' => $license
                ]
            );
        } catch (\Exception $exception) {
            return ResponseFactory::json(
                [
                    'message' => $exception->getMessage(),
                ],
                422
            );
        }
    }

    public function authorize(): bool
    {
        return true;
    }
}