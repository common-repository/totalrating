<?php

namespace TotalRatingVendors\TotalSuite\Foundation\WordPress;
! defined( 'ABSPATH' ) && exit();


use WP_Error;
use WP_Taxonomy;

/**
 * Class Taxonomy
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\WordPress
 */
abstract class Taxonomy
{
    /**
     * Taxonomy constructor.
     */
    public function __construct()
    {
        did_action('init') || doing_action('init') ? $this->register() : add_action('init', [$this, 'register']);
    }

    /**
     * Register taxonomy.
     *
     * @return WP_Error|WP_Taxonomy WP_Taxonomy on success, WP_Error otherwise.
     * @since 1.0.0
     */
    public function register()
    {
        return register_taxonomy(
            $this->getName(),
            $this->getPostTypes(),
            $this->getArguments()
        );
    }

    /**
     * @return string
     */
    protected function getName()
    {
        return null;
    }

    /**
     * @return array
     */
    protected function getPostTypes(): array
    {
        return [];
    }

    /**
     * @return array
     */
    protected function getArguments(): array
    {
        return [];
    }
}