<?php

namespace TotalRatingVendors\TotalSuite\Foundation;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Helpers\Concerns\ResolveFromContainer;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Options;

/**
 * Class License
 *
 * @package TotalSuite\Foundation
 */
class License extends Options
{
    use ResolveFromContainer;

    const REGISTERED = 'registered';
    const UNREGISTERED = 'unregistered';
    const EXPIRED = 'expired';
    const INACTIVE = 'inactive';
    const ACTIVATION_REQUIRED = 'activation_required';

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->get('status', static::UNREGISTERED);
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->get('license_key');
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->get('type', 'unknown');
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->get('valid_until');
    }

    /**
     * @return bool
     */
    public function isRegistered()
    {
        return $this->get('status', false) === static::REGISTERED;
    }

    /**
     * @return bool
     */
    public function isUnregistered()
    {
        return $this->get('status', false) === static::UNREGISTERED;
    }

    /**
     * @return bool
     */
    public function hasExpired()
    {
        if ($this->isUnregistered() || !$this->getDate()) {
            return false;
        }

        return date('Y-m-d') > $this->getDate();
    }

    /**
     * @return mixed
     */
    public function getDownloadUrl($product)
    {
        return $this->get("downloads.{$product}");
    }

    /**
     * @return mixed
     */
    public function getRenewalUrl()
    {
        return $this->get('renewal_url');
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $data = parent::toArray();

        if ($this->hasExpired()) {
            $data['status'] = License::EXPIRED;
        }

        return $data;
    }


    public function reset()
    {
        $this->fill(static::getDefault())->save();
        return $this;
    }

    /**
     * @return array
     */
    public static function getDefault()
    {
        return [
            "status" => static::UNREGISTERED,
            "license_key" => "",
            "valid_until" => "",
            "type" => "",
            "activations" => [
                "domains" => [],
                "used" => 0,
                "allowed" => 0
            ],
            "downloads" => [],
            "renewal_url" => ""
        ];
    }

    /**
     * @param $licenseData
     * @param string $status
     * @return License
     */
    public static function persist($licenseData, $status = self::REGISTERED)
    {
        $instance = static::instance();
        $instance->fill($licenseData);
        $instance['status'] = $status;
        $instance->save();

        return $instance;
    }
}