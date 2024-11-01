<?php

namespace TotalRatingVendors\TotalSuite\Foundation\WordPress\Rest;
! defined( 'ABSPATH' ) && exit();


use BadMethodCallException;
use ErrorException;
use TotalRatingVendors\TotalSuite\Foundation\Contracts\Action;
use TotalRatingVendors\TotalSuite\Foundation\Contracts\CallableResolver;
use TotalRatingVendors\TotalSuite\Foundation\ExceptionHandler;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\InvalidMethodException;
use TotalRatingVendors\TotalSuite\Foundation\Helpers\Concerns\ResolveFromContainer;
use TotalRatingVendors\TotalSuite\Foundation\Http\Body;
use TotalRatingVendors\TotalSuite\Foundation\Http\CookieJar;
use TotalRatingVendors\TotalSuite\Foundation\Http\Headers;
use TotalRatingVendors\TotalSuite\Foundation\Http\Request;
use TotalRatingVendors\TotalSuite\Foundation\Http\Response;
use TotalRatingVendors\TotalSuite\Foundation\Http\ServerContext;
use TotalRatingVendors\TotalSuite\Foundation\Http\UploadedFile;
use TotalRatingVendors\TotalSuite\Foundation\Http\Uri;
use WP_REST_Request;

/**
 * @method Route get($route, callable $callback, array $params = [], callable $permission = null)
 * @method Route post($route, callable $callback, array $params = [], callable $permission = null)
 * @method Route put($route, callable $callback, array $params = [], callable $permission = null)
 * @method Route patch($route, callable $callback, array $params = [], callable $permission = null)
 * @method Route delete($route, callable $callback, array $params = [], callable $permission = null)
 */
class Router
{
    use ResolveFromContainer;

    /**
     * @var string
     */
    protected $namespace;

    /**+
     * @var CallableResolver
     */
    protected $resolver;

    /**
     * @var Route[]
     */
    protected $routes = [];

    /**
     * @var ExceptionHandler
     */
    protected $exceptionHandler;

    /**
     * Router constructor.
     *
     * @param CallableResolver $resolver
     * @param string $namespace
     * @param ExceptionHandler $exceptionHandler
     */
    public function __construct(CallableResolver $resolver, string $namespace, ExceptionHandler $exceptionHandler)
    {
        $this->namespace = $namespace;
        $this->resolver = $resolver;
        $this->exceptionHandler = $exceptionHandler;
        add_action('rest_api_init', [$this, 'register']);
    }

    /**
     * @param CallableResolver $resolver
     */
    public function setResolver(CallableResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @throws ErrorException
     */
    public function register()
    {
        foreach ($this->routes as $route) {
            $route = $route->toArray();

            if (!register_rest_route($this->namespace, $route['route'], $route['params'])) {
                throw new ErrorException(sprintf('Route %s could not be registerd', $route['route']));
            }
        }
    }

    /**
     * @param string $methods
     * @param string $base
     * @param callable|string $callback
     * @param array $params
     * @param callable|null $permission
     *
     * @return Route
     */
    public function add(
        $methods,
        $base,
        $callback,
        array $params = [],
        callable $permission = null
    ): Route
    {
        $callback = $this->resolver->resolve($callback);

        if ($callback instanceof Action) {
            $permission = [$callback, 'authorize'];
            $params = $callback->getParams();
        }

        $route = new Route(
            $methods, $this->namespace, $base, function (WP_REST_Request $request) use ($callback) {
            $psrRequest = static::prepareRequest($request);
            $response = null;

            try {
                $response = $callback($psrRequest, $request->get_url_params());
            } catch (Exception $e) {
                $this->exceptionHandler->handle($e);
            }

            if ($response instanceof Response) {
                $response->sendAndExit();
            }

            return $response;
        }
        );

        $route->setPermission($permission);
        $route->setParams($params);

        $this->routes[] = $route;

        return $route;
    }

    /**
     * @param  WP_REST_Request  $wpRestRequest
     *
     * @return Request
     * @throws InvalidMethodException
     */
    protected static function prepareRequest(WP_REST_Request $wpRestRequest): Request
    {
        return (new Request(
            $wpRestRequest->get_method(),
            Uri::createFromString($wpRestRequest->get_route()),
            new Headers($wpRestRequest->get_headers()),
            CookieJar::createFromServer(),
            ServerContext::create($_SERVER)
                         ->toArray(),
            Body::createFromServer(),
            UploadedFile::createFromServer()
        ))->withQueryParams($wpRestRequest->get_query_params());
    }

    /**
     * @param string $name
     * @param array $arguments
     *
     * @return Route
     */
    public function __call($name, $arguments): Route
    {
        $method = strtoupper($name);

        switch ($method) {
            case 'GET':
            case 'POST':
            case 'PUT':
            case 'PATCH';
            case 'DELETE':
                array_unshift($arguments, $method);
                return call_user_func_array([$this, 'add'], $arguments);
        }

        throw new BadMethodCallException(sprintf('HTTP method %s not supported', esc_html($method)));
    }
}