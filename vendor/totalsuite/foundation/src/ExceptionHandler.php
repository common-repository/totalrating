<?php

namespace TotalRatingVendors\TotalSuite\Foundation;
! defined( 'ABSPATH' ) && exit();


use Throwable;
use TotalRatingVendors\TotalSuite\Foundation\Contracts\ExceptionHandler as ExceptionHandlerContract;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Http\Response;

/**
 * Class ExceptionHandler
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Exceptions
 */
class ExceptionHandler implements ExceptionHandlerContract
{
    /**
     * @var Environment
     */
    protected $environment;

    /**
     * ExceptionHandler constructor.
     *
     * @param Environment $environment
     */
    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @param Throwable $exception
     *
     * @throws Throwable
     */
    public function handle(Throwable $exception)
    {
        if ($this->environment->isRest()) {
            (new Response())->withStatus($this->statusFromException($exception))->withJson(
                [
                    'success' => false,
                    'error' => $exception->getMessage(),
                    'data' => ($exception instanceof Exception) ? $exception->getErrors() : [],
                ]
            )->sendAndExit();
        }

        throw $exception;
    }

    /**
     * @param Throwable $exception
     *
     * @return int|mixed
     */
    protected function statusFromException(Throwable $exception)
    {
        if ($exception instanceof Exception) {
            return $exception->getCode();
        }

        return 500;
    }


}