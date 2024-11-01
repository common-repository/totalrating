<?php

namespace TotalRatingVendors\TotalSuite\Foundation\WordPress\Rest;
! defined( 'ABSPATH' ) && exit();



use BadMethodCallException;
use TotalRatingVendors\League\Container\Container;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use RuntimeException;
use TotalRatingVendors\TotalSuite\Foundation\Contracts\Action;
use TotalRatingVendors\TotalSuite\Foundation\Contracts\CallableResolver;

class ActionResolver implements CallableResolver
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * ActionResolver constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }


    /**
     * @param $class
     *
     * @return callable
     */
    public function resolve($class): callable
    {
        $callable = null;

        if (!is_callable($class) && is_string($class) && is_subclass_of($class, Action::class)) {
            $callable = $this->createInstance($class);
        }

        if (!is_callable($callable)) {
            $message = is_array($callable) || is_object($callable) ? json_encode($callable) : $callable;
            throw new BadMethodCallException(sprintf('Cannot resolve %s', $message));
        }

        return $callable;
    }

    protected function createInstance($class)
    {
        $reflection = new ReflectionClass($class);

        if (!$reflection->isInstantiable()) {
            throw new RuntimeException(sprintf('%s is not instantiable', $class));
        }

        $constructor = $reflection->getConstructor();

        if ($constructor === null || $constructor->getNumberOfParameters() === 0) {
            return new $class();
        }

        $arguments = $this->getParameters($constructor);

        return $reflection->newInstanceArgs($arguments);
    }

    /**
     * @param ReflectionMethod $method
     *
     * @return array resolved constructor parameters
     * @throws ReflectionException
     */
    protected function getParameters(ReflectionMethod $method)
    {
        $arguments = [];

        foreach ($method->getParameters() as $parameter) {
            if ($this->container->has($parameter->getName())) {
                $arguments[] = $this->container->get($parameter->getName());
                continue;
            }

            if ($this->container->has($parameter->getName())) {
                $arguments[] = $this->container->get($parameter->getName());
                continue;
            }

            $class = $parameter->getType();

            if ($class !== null) {
                $arguments[] = $this->container->get(method_exists($class, 'getName') ? $class->getName() : (string)$class);
                continue;
            }

            if ($parameter->isDefaultValueAvailable()) {
                $arguments[] = $parameter->getDefaultValue();
                continue;
            }

            throw new RuntimeException(sprintf('Cannot resolve parameter %s', $parameter->getName()));
        }

        return $arguments;
    }
}