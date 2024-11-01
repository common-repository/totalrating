<?php

namespace TotalRatingVendors\TotalSuite\Foundation\WordPress;
! defined( 'ABSPATH' ) && exit();


/**
 * Class Shortcode
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\WordPress
 */
abstract class Shortcode
{
    /**
     * @var string
     */
    protected $tag;

    /**
     * @var array $attributes
     */
    protected $attributes = [];

    /**
     * @var string $content
     */
    protected $content;

    /**
     * Setup shortcode.
     *
     * @param string $tag
     *
     * @since 1.0.0
     */
    public function __construct(string $tag)
    {
        $this->tag = $tag;

        add_shortcode($tag, $this);
    }

    /**
     * Get attribute value.
     *
     * @param      $name
     * @param null $default
     *
     * @return mixed|null
     * @since 1.0.0
     */
    public function getAttribute($name, $default = null)
    {
        return $this->attributes[$name] ?? $default;
    }

    /**
     * Get content.
     *
     * @return string
     * @since 1.0.0
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param array       $attributes
     * @param string|null $content
     *
     * @return string
     */
    public function __invoke($attributes = [], string $content = '')
    {
        $this->attributes = (array)$attributes;
        $this->content    = trim($content);

        return $this->render();
    }

    /**
     * @return string
     */
    abstract public function render(): string;
}