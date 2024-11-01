<?php

namespace TotalRating\Actions\Concerns;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Plugin;
use TotalRatingVendors\TotalSuite\Foundation\Helpers\ReCaptcha;
use TotalRatingVendors\TotalSuite\Foundation\Http\ResponseFactory;

trait ChecksReCaptcha
{
    /**
     * @inheritDoc
     */
    public function checkRecaptcha(): bool
    {
        if (Plugin::options('advanced.recaptcha.enabled', false)) {
            $secret    = Plugin::options('advanced.recaptcha.secret');
            $threshold = Plugin::options('advanced.recaptcha.threshold');
            $token     = Plugin::request('recaptcha');
            $ip        = Plugin::request()
                               ->ip();

            if (!ReCaptcha::check($token, $threshold, $secret, $ip)) {
                ResponseFactory::json('Invalid reCaptcha. Please try again.', 403)
                               ->sendAndExit();

                return false;
            }
        }

        return true;
    }
}
