<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Http;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Contracts\Support\Arrayable;
use TotalRatingVendors\TotalSuite\Foundation\Helpers\Concerns\ResolveFromContainer;

/**
 * Class Cookies
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Http
 */
class CookieJar implements Arrayable
{
    use ResolveFromContainer;

    /**
     * @var Cookie[]
     */
    protected $cookies = [];

    /**
     * CookieJar constructor.
     *
     * @param Cookie[] $cookies
     */
    public function __construct(array $cookies = [])
    {
        $this->cookies = $cookies;
    }

    /**
     * @return CookieJar
     */
    public static function createFromServer()
    {
        return static::createFromArray($_COOKIE);
    }

    /**
     * @param array $cookies
     *
     * @return CookieJar
     */
    public static function createFromArray(array $cookies)
    {
        $decoded = [];

        foreach ($cookies as $name => $value) {
            $decoded[$name] = new Cookie($name, $value);
        }

        return new static($decoded);
    }

    /**
     * @return Cookie[]
     */
    public function getCookies(): array
    {
        return $this->cookies;
    }

    /**
     * @param $name
     *
     * @return Cookie|null
     */
    public function getCookie($name)
    {
        return $this->cookies[$name] ?? null;
    }

    /**
     * @param string $name
     * @param string $value
     * @param int $expires
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httpOnly
     * @param string $sameSite
     */
    public function set(
        $name,
        $value,
        $expires = 0,
        $path = '',
        $domain = '',
        $secure = false,
        $httpOnly = false,
        $sameSite = 'lax'
    )
    {
        $this->cookies[$name] = static::make($name, $value, $expires, $path, $domain, $secure, $httpOnly, $sameSite);
    }

    /**
     * @param string $name
     * @param string $value
     * @param int $expires
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httpOnly
     * @param string $sameSite
     *
     * @return Cookie
     */
    public static function make(
        $name,
        $value,
        $expires = 0,
        $path = '',
        $domain = '',
        $secure = false,
        $httpOnly = false,
        $sameSite = 'lax'
    )
    {
        return new Cookie($name, $value, $expires, $path, $domain, $secure, $httpOnly, $sameSite);
    }

    /**
     * @param string $name
     * @param null $default
     *
     * @return mixed
     */
    public function get($name, $default = null)
    {
        $cookie = $this->cookies[$name] ?? null;

        if ($cookie instanceof Cookie) {
            return $cookie->getValue();
        }

        return $default;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->cookies);
    }

    /**
     *
     */
    public function send()
    {
        foreach ($this->cookies as $cookie) {
            $cookie->send();
        }
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $cookies = [];

        foreach ($this->cookies as $name => $cookie) {
            $cookies[$name] = $cookie->getValue();
        }

        return $cookies;
    }
}
