<?php

namespace TotalRatingVendors\TotalSuite\Foundation\WordPress\Actions\Activation;
! defined( 'ABSPATH' ) && exit();



use TotalRatingVendors\TotalSuite\Foundation\Action;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Http\ResponseFactory;
use TotalRatingVendors\TotalSuite\Foundation\License;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Tasks\Activation\RemoveLicense as RemoveLicenseTask;

class Unlink extends Action
{
    public function authorize(): bool
    {
        return true;
    }

    protected function execute()
    {
        $license = License::instance();

        try {
            RemoveLicenseTask::invoke();
        } catch (Exception $exception) {
            ResponseFactory::json([
                'message' => $exception->getMessage(),
                'license' => $license
            ], 422);
        }

        return ResponseFactory::json($license);
    }
}