<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Http;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Contracts\Http\HeadersInterface;

/**
 * Class Headers
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Http
 */
class Headers implements HeadersInterface
{
    /**
     * @var string[]
     */
    protected $originalKeys = [];

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * Headers constructor.
     *
     * @param array $headers
     */
    public function __construct(array $headers = [])
    {
        foreach ($headers as $key => $header) {
            $this->set($key, $header);
        }
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        $originalKey              = $key;
        $key                      = $this->key($key);
        $this->originalKeys[$key] = $originalKey;
        $this->headers[$key]      = is_array($value) ? $value : [$value];
    }

    /**
     * @param $key
     *
     * @return string
     */
    public function key($key): string
    {
        if (!array_key_exists($key, $this->originalKeys)) {
            $key = str_replace('_', '-', strtolower($key));

            if (strpos($key, 'http-') === 0) {
                $key = substr($key, 5);
            }
        }

        return $key;
    }

    /**
     * @param ServerContext $context
     *
     * @return Headers
     */
    public static function createFromServer(ServerContext $context): Headers
    {
        static $special = [
            'CONTENT_TYPE'    => 1,
            'CONTENT_LENGTH'  => 1,
            'PHP_AUTH_USER'   => 1,
            'PHP_AUTH_PW'     => 1,
            'PHP_AUTH_DIGEST' => 1,
            'AUTH_TYPE'       => 1,
        ];

        $data    = [];
        $context = self::determineAuthorization($context);

        foreach ($context as $key => $value) {
            $key = strtoupper($key);
            if (isset($special[$key]) || (strpos($key, 'HTTP_') === 0 && $key !== 'HTTP_CONTENT_LENGTH')) {
                $data[$key] = $value;
            }
        }

        return new static($data);
    }

    /**
     * If HTTP_AUTHORIZATION does not exist tries to get it from getallheaders() when available.
     *
     * @param ServerContext $context
     *
     * @return ServerContext
     */
    public static function determineAuthorization(ServerContext $context): ServerContext
    {
        $authorization = $context->get('HTTP_AUTHORIZATION');
        if (!empty($authorization) || !is_callable('getallheaders')) {
            return $context;
        }

        $headers = getallheaders();
        if (!is_array($headers)) {
            return $context;
        }

        $headers = array_change_key_case($headers, CASE_LOWER);
        if (isset($headers['authorization'])) {
            $context->set('HTTP_AUTHORIZATION', $headers['authorization']);
        }

        return $context;
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function add($key, $value)
    {
        $oldValues = $this->get($key, []);
        $newValues = is_array($value) ? $value : [$value];
        $this->set($key, array_merge($oldValues, array_values($newValues)));
    }

    /**
     * @param $key
     * @param $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if ($this->has($key)) {
            return $this->headers[$this->key($key)];
        }

        return $default;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has($key): bool
    {
        return array_key_exists($this->key($key), $this->headers);
    }

    /**
     * @return array
     */
    public function all(): array
    {
        $withOriginalKeys = [];

        foreach ($this->headers as $key => $value) {
            $originalKey                    = $this->originalKeys[$key];
            $withOriginalKeys[$originalKey] = $value;
        }

        return $withOriginalKeys;
    }

    /**
     * @param      $key
     * @param null $default
     *
     * @return string|null
     */
    public function getOriginalKey($key, $default = null)
    {
        return $this->originalKeys[$key] ?? $default;
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * @param string $key
     */
    public function remove($key)
    {
        $key = $this->key($key);
        unset($this->originalKeys[$key], $this->headers[$key]);
    }
}