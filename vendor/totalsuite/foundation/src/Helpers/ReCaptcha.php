<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Helpers;
! defined( 'ABSPATH' ) && exit();


class ReCaptcha
{
    /**
     * @var string
     */
    protected $endpoint;

    /**
     * @var string
     */
    protected $secret;

    /**
     * @var float
     */
    protected $threshold;

    /**
     * ReCaptcha constructor.
     *
     * @param string $secret
     * @param float $threshold
     */
    public function __construct(string $secret, float $threshold = 0.5)
    {
        $this->secret = $secret;
        $this->endpoint = 'https://www.google.com/recaptcha/api/siteverify';
        $this->threshold = $threshold;
    }

    /**
     * @param $token
     * @param $ip
     *
     * @return bool
     */
    public function verify($token, $ip = null)
    {
        $request = curl_init();
        curl_setopt($request, CURLOPT_URL, $this->getEndpoint());
        curl_setopt($request, CURLOPT_POST, 3);
        curl_setopt(
            $request,
            CURLOPT_POSTFIELDS,
            [
                'secret' => $this->getSecret(),
                'response' => $token,
                'remoteip' => $ip,
            ]
        );
        curl_setopt($request, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($request, CURLOPT_TIMEOUT, 60);

        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);

        $response = json_decode(curl_exec($request), true);

        curl_close($request);

        return $response['success'] === true && $response['score'] >= $this->threshold;
    }

    /**
     * @param string $endpoint
     *
     * @return ReCaptcha
     */
    public function setEndpoint(string $endpoint): ReCaptcha
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * @param string $secret
     *
     * @return ReCaptcha
     */
    public function setSecret(string $secret): ReCaptcha
    {
        $this->secret = $secret;

        return $this;
    }

    /**
     * @return string
     */
    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * @param string $token
     * @param float $threshold
     * @param string $secret
     * @param string|null $ip
     *
     * @return bool
     */
    public static function check($token, $threshold, $secret, $ip = null): bool
    {
        $instance = new static($secret, $threshold);

        return $instance->verify($token, $ip);
    }
}