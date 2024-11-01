<?php

namespace TotalRating;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Admin\EntityFilter;
use TotalRating\Entities\EntityManager;
use TotalRating\Entities\Resolvers\CommentsResolver;
use TotalRating\Entities\Resolvers\CustomResolver;
use TotalRating\Entities\Resolvers\PostsResolver;
use TotalRating\Entities\Resolvers\TaxonomiesResolver;
use TotalRating\Entities\Resolvers\UsersResolver;
use TotalRating\Entities\Resolvers\WidgetsResolver;
use TotalRating\Handlers\HandleDisplayResults;
use TotalRating\Handlers\HandleDisplayWidget;
use TotalRating\Metaboxes\RatingMetabox;
use TotalRating\Services\WorkflowRegistry;
use TotalRatingVendors\League\Container\Container;
use TotalRatingVendors\League\Container\ServiceProvider\AbstractServiceProvider;
use TotalRatingVendors\TotalSuite\Foundation\Http\Request;
use TotalRatingVendors\TotalSuite\Foundation\View\Engine;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Modules\Manager;

class ServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        EntityManager::class,
        HandleDisplayWidget::class,
        HandleDisplayResults::class,
        WorkflowRegistry::class,
        RatingMetabox::class,
        EntityFilter::class,
    ];

    /**
     * @inheritDoc
     */
    public function register()
    {
        /**
         * @var $container Container
         */
        $container = $this->getContainer();

        $container->share(
            EntityManager::class,
            static function () {
                $manager = new EntityManager();
                $manager->registerResolver(new UsersResolver());
                $manager->registerResolver(new PostsResolver());
                $manager->registerResolver(new TaxonomiesResolver());
                $manager->registerResolver(new CommentsResolver());
                $manager->registerResolver(new WidgetsResolver());
                $manager->registerResolver(new CustomResolver());

                return $manager;
            }
        );

        $container->share(HandleDisplayWidget::class)
                  ->addArgument($container->get(EntityManager::class))
                  ->addArgument($container->get(Manager::class));

        $container->share(HandleDisplayResults::class)
                  ->addArgument($container->get(EntityManager::class))
                  ->addArgument($container->get(Manager::class));

        $container->share(WorkflowRegistry::class, WorkflowRegistry::class);

        $container->share(RatingMetabox::class)
                  ->addArgument($container->get(Engine::class))
                  ->addArgument($container->get(Manager::class))
                  ->addArgument(esc_html__('Ratings', 'totalrating'))
                  ->resolve();

        $container->share(EntityFilter::class)
                  ->addArgument($container->get(Request::class));
    }


}
