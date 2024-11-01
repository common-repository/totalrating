<?php

namespace TotalRatingVendors\TotalSuite\Foundation\WordPress\Actions\Modules;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Action;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Http\Response;
use TotalRatingVendors\TotalSuite\Foundation\Http\ResponseFactory;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Modules\Manager;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Tasks\Modules\DeactivateModule;

class Deactivate extends Action
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
     * @param $moduleUid
     *
     * @return Response
     * @throws Exception
     */
    public function execute($moduleUid): Response
    {
        DeactivateModule::invoke($this->manager, $moduleUid);

        return ResponseFactory::json($moduleUid);
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
