<?php

namespace TotalRatingVendors\TotalSuite\Foundation\WordPress\Admin;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Http\Request;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Plugin;
use WP_Query;

abstract class Filter
{
    /**
     * ListFilter constructor.
     *
     */
    public function __construct()
    {
        global $pagenow;

        if (is_admin() && $pagenow === 'edit.php') {
            $this->register();
        }
    }

    /**
     * @return void
     */
    protected function register()
    {
        add_action('restrict_manage_posts', [$this, 'render']);
        add_action('pre_get_posts', [$this, 'query']);
    }

    /**
     * @return void
     */
    public function render()
    {
        global $post_type;

        if ($this->check($post_type)) {
            /**
             * @var Request $request
             */
            $request = Plugin::get(Request::class);
            $this->input($request);
        }
    }

    /**
     * @param WP_Query $query
     */
    public function query(WP_Query $query)
    {
        global $post_type;

        if ($this->check($post_type)) {
            /**
             * @var Request $request
             */
            $request = Plugin::get(Request::class);
            $this->filter($query, $request);
        }
    }

    /**
     * @param string|null $post_type
     * @return bool
     */
    abstract protected function check($post_type = null);

    /**
     * @param Request $request
     *
     * @return mixed
     */
    abstract protected function input(Request $request);

    /**
     * @param WP_Query $query
     * @param Request $request
     *
     * @return mixed
     */
    abstract protected function filter(WP_Query $query, Request $request);
}