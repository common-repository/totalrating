<?php

namespace TotalRatingVendors\TotalSuite\Foundation\WordPress\Admin;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\League\Plates\Engine;
use TotalRatingVendors\TotalSuite\Foundation\View;

/**
 * Class Page
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\WordPress\Admin
 */
abstract class Page
{
    const BELOW_POSTS = 5;
    const BELOW_MEDIA = 10;
    const BELOW_LINKS = 15;
    const BELOW_PAGES = 20;
    const BELOW_COMMENTS = 25;
    const BELOW_FIRST_SEPARATOR = 60;
    const BELOW_PLUGINS = 65;
    const BELOW_USERS = 70;
    const BELOW_TOOLS = 75;
    const BELOW_SETTINGS = 80;
    const BELOW_SECOND_SEPARATOR = 100;

    /**
     * @var Engine
     */
    protected static $engine;

    /**
     * @var string
     */
    protected $template = '';

    /**
     * Page constructor.
     */
    public function __construct()
    {
        add_action('admin_menu', [$this, 'register']);

        add_action('admin_enqueue_scripts', function () {
            if ($GLOBALS['pagenow'] === 'admin.php' && isset($_REQUEST['page']) && $_REQUEST['page'] === $this->slug()) {
                $this->assets();
            }
        });
    }

    /**
     * @return string
     */
    public function slug(): string
    {
        return sanitize_title_with_dashes($this->title());
    }

    /**
     * @return string
     */
    abstract public function title(): string;

    /**
     * @param Engine $engine
     */
    public static function setEngine(Engine $engine)
    {
        static::$engine = $engine;
    }

    public function register()
    {
        add_menu_page(
            $this->title(),
            $this->title(),
            $this->capability(),
            $this->slug(),
            [$this, 'render'],
            $this->icon(),
            $this->position()
        );
    }

    /**
     * @return string
     */
    public function capability(): string
    {
        return 'manage_options';
    }

    /**
     * @return string
     */
    public function icon(): string
    {
        return 'dashicons-admin-generic';
    }

    /**
     * @return int
     */
    public function position(): int
    {
        return static::BELOW_COMMENTS;
    }

    public function render()
    {
        echo static::$engine->render($this->template(), $this->data());
    }

    /**
     * @return string
     */
    abstract public function template(): string;

    /**
     * @return array
     */
    public function data(): array
    {
        return [];
    }

    /**
     * @return void
     */
    abstract public function assets();
}