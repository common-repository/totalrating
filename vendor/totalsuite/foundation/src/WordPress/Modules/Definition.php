<?php

namespace TotalRatingVendors\TotalSuite\Foundation\WordPress\Modules;
! defined( 'ABSPATH' ) && exit();


use ArrayAccess;
use TotalRatingVendors\TotalSuite\Foundation\Contracts\Support\Arrayable;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\ModuleException;
use TotalRatingVendors\TotalSuite\Foundation\Http\Concerns\WithJsonResponse;
use TotalRatingVendors\TotalSuite\Foundation\Support\Arrays;

/**
 * Class Definition
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\WordPress\Module
 */
class Definition implements ArrayAccess, Arrayable
{
    use WithJsonResponse;

    const TYPE_EXTENSION = 'extension';
    const TYPE_TEMPLATE = 'template';

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * Definition constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        static $defaults = [
            'id' => null,
            'type' => null,
            'name' => null,
            'description' => null,
            'images' => [
                'icon' => null,
                'cover' => null,
            ],
            'author' => [
                'name' => 'totalsuite',
                'email' => 'contact@totalsuite.net',
            ],
            'url' => null,
            'baseUrl' => null,
            'version' => null,
            'requires' => null,
            'options' => [],
            'builtIn' => false,
            'installed' => false,
            'price' => 0.0,
            'downloadUrl' => null,
            'lastVersion' => null,
            'purchased' => false,
            'path' => null,
            'class' => null,
            'activated' => false,
            'refreshOnActivation' => false,
        ];

        $this->attributes = array_merge($defaults, $attributes);
    }

    /**
     * @return bool
     */
    public function isInstalled(): bool
    {
        return $this->attributes['installed'];
    }

    /**
     * @return bool|int
     */
    public function isUpdated()
    {
        return version_compare($this->version, $this->lastVersion, '>=');
    }

    /**
     * @return bool
     */
    public function isFree(): bool
    {
        return $this->price === 0.0;
    }

    /**
     * @return bool
     */
    public function isBuiltIn(): bool
    {
        return $this->builtIn === true;
    }

    /**
     * @return bool
     */
    public function isExtension()
    {
        return $this->get('type') === self::TYPE_EXTENSION;
    }

    /**
     * @return bool
     */
    public function isTemplate()
    {
        return $this->get('type') === self::TYPE_TEMPLATE;
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->attributes;
    }

    public function validate()
    {
        if (empty($this->id) || !is_string($this->id)) {
            throw new ModuleException('Module must have a unique id and must be a string');
        }

        if (!in_array($this->type, ['template', 'extension'], true)) {
            throw new ModuleException(
                sprintf(
                    'Invalid type attributes in module definition must be one of [%s]',
                    implode(
                        ', ',
                        [
                            'template',
                            'extension',
                        ]
                    )
                )
            );
        }

        if (empty($this->version) || !is_string($this->version)) {
            throw new ModuleException('Module must have a version and must be a string');
        }
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * @param $key
     * @param $value
     *
     * @return mixed
     */
    public function __set($key, $value)
    {
        return $this->set($key, $value);
    }

    /**
     * @param      $key
     * @param null $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arrays::get($this->attributes, $key, $default);
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return $this->has($key);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return Arrays::has($this->attributes, $key);
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * @param $key
     * @param $value
     *
     * @return self
     */
    public function set($key, $value): self
    {
        Arrays::set($this->attributes, $key, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
    }
}