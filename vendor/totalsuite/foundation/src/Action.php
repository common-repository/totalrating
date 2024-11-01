<?php

namespace TotalRatingVendors\TotalSuite\Foundation;
! defined( 'ABSPATH' ) && exit();


use InvalidArgumentException;
use TotalRatingVendors\TotalSuite\Foundation\Contracts\Action as ActionContract;
use TotalRatingVendors\TotalSuite\Foundation\Http\Request;

/**
 * Class Action
 *
 * @package TotalSuite\Foundation
 */
abstract class Action implements ActionContract
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @return array
     */
    public function getParams(): array
    {
        return [];
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param Request $request
     * @param array $arguments
     *
     * @return mixed
     */
    final public function __invoke(Request $request, array $arguments = [])
    {
        if (!method_exists($this, 'execute')) {
            throw new InvalidArgumentException(sprintf('Method "execute" not defined in %s', static::class));
        }

        $this->request = $request;
        return call_user_func_array([$this, 'execute'], array_values($arguments));
    }
}