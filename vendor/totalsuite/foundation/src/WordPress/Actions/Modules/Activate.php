<?php

namespace TotalRatingVendors\TotalSuite\Foundation\WordPress\Actions\Modules;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Action;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Http\Response;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Modules\Manager;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Tasks\Modules\ActivateModule;

class Activate extends Action
{
    /**
     * @var Manager
     */
    protected $manager;

    /**
     * Index constructor.
     *
     * @param Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param $moduleId
     *
     * @return Response
     * @throws Exception
     */
    public function execute($moduleId): Response
    {
        return ActivateModule::invoke($this->manager, $moduleId)
                             ->toJsonResponse();
    }

    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return [
            'moduleId' => [
                'expression' => '(?<moduleId>([\w-]+))',
                'sanitize_callback' => static function ($moduleId) {
                    return (string)$moduleId;
                },
            ],
        ];
    }
}
