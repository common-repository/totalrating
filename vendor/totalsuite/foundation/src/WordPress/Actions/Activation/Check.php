<?php

namespace TotalRatingVendors\TotalSuite\Foundation\WordPress\Actions\Activation;
! defined( 'ABSPATH' ) && exit();



use TotalRatingVendors\TotalSuite\Foundation\Action;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Http\Response;
use TotalRatingVendors\TotalSuite\Foundation\Http\ResponseFactory;
use TotalRatingVendors\TotalSuite\Foundation\License;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Tasks\Activation\CheckLicense as CheckLicenseTask;

class Check extends Action
{

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return Response
     */
    protected function execute()
    {
        try {
            CheckLicenseTask::invoke();
        } catch (Exception $exception) {
            ResponseFactory::json(
                [
                    'message' => $exception->getMessage(),
                    'license' => License::instance()
                ],
                422
            );
        }

        return ResponseFactory::json(['license' => License::instance()]);
    }
}