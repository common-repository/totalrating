<?php

namespace TotalRatingVendors\TotalSuite\Foundation\WordPress\Tasks\Modules;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Events\Modules\OnDeactivateModule;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Task;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Modules\Manager;

/**
 * Class DeactivateModule
 *
 * @method static bool invoke(Manager $manager, string $moduleId)
 * @method static bool invokeWithFallback($fallback, Manager $manager, string $moduleId)
 */
class DeactivateModule extends Task
{

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var string
     */
    protected $moduleId;

    /**
     * Activate constructor.
     *
     * @param Manager $manager
     * @param string $moduleId
     */
    public function __construct(Manager $manager, string $moduleId)
    {
        $this->manager = $manager;
        $this->moduleId = $moduleId;
    }

    /**
     * @inheritDoc
     */
    protected function validate()
    {
        return $this->manager->getDefinition($this->moduleId) !== null;
    }

    /**
     * @inheritDoc
     */
    protected function execute()
    {
        Exception::throwUnless($this->manager->deactivate($this->moduleId), 'Cannot activate module');
        $definition = $this->manager->getDefinition($this->moduleId);

        OnDeactivateModule::emit($definition);

        return true;
    }
}
