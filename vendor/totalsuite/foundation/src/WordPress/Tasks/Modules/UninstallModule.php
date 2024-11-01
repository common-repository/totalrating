<?php

namespace TotalRatingVendors\TotalSuite\Foundation\WordPress\Tasks\Modules;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Events\Modules\OnUninstallModule;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Task;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Modules\Manager;

/**
 * Class UninstallModule
 *
 * @method static bool invoke(Manager $manager, string $moduleId)
 * @method static bool invokeWithFallback($fallback, Manager $manager, string $moduleId)
 */
class UninstallModule extends Task
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
     * @throws Exception
     */
    protected function execute()
    {
        $definition = $this->manager->getDefinition($this->moduleId);

        Exception::throwUnless(
            $this->manager->uninstall($this->moduleId),
            'Cannot uninstall module'
        );

        OnUninstallModule::emit($definition);

        return true;
    }
}
