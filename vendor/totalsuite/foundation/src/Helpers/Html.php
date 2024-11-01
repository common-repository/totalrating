<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Helpers;
! defined( 'ABSPATH' ) && exit();



use TotalRatingVendors\TotalSuite\Foundation\Helpers\Concerns\Attributes;

/**
 * Class Html
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Helpers
 */
class Html
{
    use Attributes;

    public const EMPTY_TAG = '';

    /**
     * @var array
     */
    protected static $voidElements = [
        '',
        'area',
        'base',
        'br',
        'col',
        'command',
        'embed',
        'hr',
        'img',
        'input',
        'keygen',
        'link',
        'meta',
        'param',
        'source',
        'track',
        'wbr',
    ];

    /**
     * @var string
     */
    protected $tag;


    /**
     * @var array
     */
    protected $content = [];

    /**
     * Element constructor.
     *
     * @param string     $tag
     * @param array      $attributes
     * @param mixed|null $content
     */
    public function __construct(string $tag, array $attributes = [], $content = null)
    {
        $this->tag = $tag;
        $this->setAttributes($attributes);
        $this->setContent($content);
    }

    /**
     * @param mixed $content
     *
     * @return Html
     */
    public function setContent($content)
    {
        if (is_array($content)) {
            $this->content = $content;
        } else {
            $this->content[] = $content;
        }

        return $this;
    }

    /**
     * @param        $tag
     * @param array  $attributes
     * @param string $content
     *
     * @return static
     */
    public static function create($tag, array $attributes = [], $content = '')
    {
        return new static($tag, $attributes, $content);
    }

    /**
     * @param mixed $content
     * @param bool  $prepend
     *
     * @return Html
     */
    public function addContent($content, $prepend = false)
    {
        if (is_array($content)) {
            $this->content = array_merge($this->content, $content);
        } elseif ($prepend) {
            array_unshift($this->content, $content);
        } else {
            $this->content[] = $content;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * @return string
     */
    public function render()
    {
        return $this->open() . $this->content() . $this->close();
    }

    /**
     * @return string
     */
    public function open()
    {
        if (empty($this->tag)) {
            return '';
        }

        $attributes = $this->buildAttributes($this->attributes);

        $html = '<' . $this->tag;
        $html .= empty($attributes) ? '' : ' ' . $attributes;
        $html .= $this->isVoid() ? ' />' : '>';

        return $html;
    }

    /**
     * @param array $attributes
     *
     * @return string
     */
    protected function buildAttributes(array $attributes)
    {
        $parsed = [];

        foreach ($attributes as $key => $value) {
            $value = implode(' ', $value);

            if (is_string($key)) {
                $parsed[] = sprintf('%s="%s"', $key, $this->escapeAttribute($value));
            } else {
                $parsed[] = $this->escapeAttribute($value);
            }
        }

        return implode(' ', $parsed);
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function escapeAttribute($value)
    {
        return htmlspecialchars($value, ENT_QUOTES);
    }

    /**
     * @return bool
     */
    protected function isVoid()
    {
        return in_array($this->tag, static::$voidElements);
    }

    /**
     * @return string
     */
    public function content()
    {
        return implode('', $this->content);
    }

    /**
     * @return string
     */
    public function close()
    {
        if ($this->isVoid()) {
            return '';
        }

        return '</' . $this->tag . '>';
    }
}