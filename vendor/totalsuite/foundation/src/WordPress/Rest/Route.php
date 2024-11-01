<?php

namespace TotalRatingVendors\TotalSuite\Foundation\WordPress\Rest;
! defined( 'ABSPATH' ) && exit();


use BadMethodCallException;
use InvalidArgumentException;
use TotalRatingVendors\TotalSuite\Foundation\Contracts\Support\Arrayable;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Capability;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Roles;

/**
 * Class Route
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\WordPress\Rest
 */
class Route implements Arrayable
{
    /**
     * @var string
     */
    protected static $adminLevel = Roles::ADMINISTRATOR;

    /**
     * @var array
     */
    protected static $allowedMethods = ['GET', 'POST', 'PUT', 'DELETE', 'OPTION', 'HEAD', 'PATCH'];

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var
     */
    protected $base;

    /**
     * @var string
     */
    protected $methods = 'GET';

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @var callable
     */
    protected $callback;

    /**
     * @var callable
     */
    protected $permission;

    /**
     * @var bool
     */
    protected $admin = false;

    /**
     * @var string
     */
    protected $capability = null;

    /**
     * Route constructor.
     *
     * @param string $methods
     * @param string $namespace
     * @param string $name
     * @param callable $callback
     */
    public function __construct(string $methods, string $namespace, string $name, callable $callback)
    {
        $this->namespace = $namespace;
        $this->setMethods($methods);
        $this->setBase($name);
        $this->setCallback($callback);
    }

    /**
     * @param bool $secure
     *
     * @return Route
     */
    public function admin($secure = true)
    {
        $this->admin = $secure;

        return $this;
    }

    /**
     * @param string $capability
     *
     * @return Route
     */
    public function capability($capability)
    {
        $this->capability = $capability;

        return $this;
    }

    /**
     * @return string
     */
    public function getMethods(): string
    {
        return $this->methods;
    }

    /**
     * @param array|string $methods
     *
     * @return $this
     * @throws BadMethodCallException
     */
    public function setMethods($methods): self
    {
        if (empty(array_intersect(explode(',', strtoupper($methods)), static::$allowedMethods))) {
            throw new BadMethodCallException(
                sprintf(
                    'Only %s methods are allowed',
                    implode(', ', static::$allowedMethods)
                )
            );
        }

        $this->methods = strtoupper($methods);

        return $this;
    }

    /**
     * @return callable
     */
    public function getCallback(): callable
    {
        return $this->callback;
    }

    /**
     * @param callable $callback
     *
     * @return Route
     */
    public function setCallback(callable $callback): Route
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * @return callable
     */
    public function getPermission(): callable
    {
        return $this->permission;
    }

    /**
     * @param callable $permission
     *
     * @return Route
     */
    public function setPermission($permission): self
    {
        $this->permission = $permission;

        return $this;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     *
     * @return $this
     */
    public function setParams(array $params): self
    {
        $defaults = [
            'expression' => '',
            'default' => null,
            'required' => true,
            'validation_callback' => null,
            'sanitize_callback' => null,
        ];

        $params = array_map(
            function ($param) use ($defaults) {
                return array_merge($defaults, $param);
            },
            $params
        );

        foreach ($params as $name => $param) {
            $this->setParam(
                $name,
                $param['expression'],
                $param['default'],
                $param['required'],
                $param['validation_callback'] ?? null,
                $param['sanitize_callback'] ?? null
            );
        }

        return $this;
    }

    /**
     * @param array|string $name
     * @param string $expression
     * @param null $default
     * @param bool $required
     * @param callable|null $validation
     * @param callable|null $sanitize
     *
     * @return $this
     */
    public function setParam(
        $name,
        string $expression,
        $default = null,
        bool $required = true,
        callable $validation = null,
        callable $sanitize = null
    ): self
    {
        $this->params[$name] = [
            'expression' => $expression,
            'default' => $default,
            'required' => $required,
            'validation_callback' => $validation,
            'sanitize_callback' => $sanitize,
        ];

        return $this;
    }

    /**
     * @param $name
     *
     * @return mixed|null
     */
    public function getParam($name)
    {
        if (array_key_exists($name, $this->params)) {
            return $this->params[$name];
        }

        return null;
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
     * @return array
     */
    public function toArray(): array
    {
        $route = [
            'route' => $this->getBase(),
            'params' => [
                'methods' => $this->methods,
                'callback' => $this->callback,
                'permission_callback' => function(){
                    if ($this->capability !== null && !forward_static_call([$this->capability, 'check'])){
                        return false;
                    }

                    return call_user_func($this->permission);
                },
            ],
        ];

        if (!empty($this->params)) {
            $args = [];

            $route['route'] = rtrim($this->base, '/');

            foreach ($this->params as $name => $options) {
                if ($options['required']) {
                    $route['route'] .= '/' . $options['expression'];
                } else {
                    $route['route'] .= '(?:/' . $options['expression'] . ')?';
                }

                $args[$name] = $options;
            }

            $route['params']['args'] = $args;
        }


        return $route;
    }

    /**
     * @return mixed
     */
    public function getBase()
    {
        return $this->base;
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function setBase($name): self
    {
        if (empty($name)) {
            throw new InvalidArgumentException(sprintf('Route name must not be empty'));
        }

        $this->base = trim($name);

        return $this;
    }

    /**
     * @return string
     */
    public static function getAdminLevel(): string
    {
        return self::$adminLevel;
    }

    /**
     * @param string $adminLevel
     */
    public static function setAdminLevel(string $adminLevel)
    {
        self::$adminLevel = $adminLevel;
    }

    /**
     * @return bool
     */
    public function isLimitedToAdmin(): bool
    {
        return $this->admin;
    }

    /**
     * @return bool
     */
    public function isRestrictedByCapability(): bool
    {
        return $this->capability !== null;
    }

    /**
     * @return string|null
     */
    public function getCapability()
    {
        return $this->capability;
    }
}