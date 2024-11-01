<?php

namespace TotalRating\Tasks\Widget;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Entities\EntityManager;
use TotalRating\Models\Widget;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Support\Collection;
use TotalRatingVendors\TotalSuite\Foundation\Task;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Modules\Manager;

/**
 * Class DisplayWidget
 *
 * @package TotalRating\Tasks\Widget
 * @method static Collection|null invoke(Manager $manager, EntityManager $resolver)
 */
class SetupWidgetsFromContext extends Task
{
    /**
     * @var EntityManager
     */
    protected $resolver;

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * constructor.
     *
     * @param Manager       $manager
     * @param EntityManager $resolver
     */
    public function __construct(Manager $manager, EntityManager $resolver)
    {
        $this->resolver = $resolver;
        $this->manager  = $manager;
    }


    /**
     * @inheritDoc
     */
    protected function validate()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function execute()
    {
        add_action(
            'template_redirect',
            function () {
                if (!is_admin()) {
                    $this->display();
                }
            }
        );
    }

    /**
     * @return Collection<Widget>|null
     * @throws Exception
     */
    protected function display()
    {
        $context = GetContext::invoke($this->resolver);

        if ($context === null) {
            return null;
        }

        return SetupFilter::invoke($this->manager, $context);
    }

}
