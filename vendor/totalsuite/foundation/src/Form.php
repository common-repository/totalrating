<?php

namespace TotalRatingVendors\TotalSuite\Foundation;
! defined( 'ABSPATH' ) && exit();


use ArrayAccess;
use TotalRatingVendors\League\Plates\Engine;
use TotalRatingVendors\TotalSuite\Foundation\Contracts\Support\Arrayable;
use TotalRatingVendors\TotalSuite\Foundation\Contracts\Support\HTMLRenderable;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Form\Concerns\Types;
use TotalRatingVendors\TotalSuite\Foundation\Form\Field;
use TotalRatingVendors\TotalSuite\Foundation\Helpers\Concerns\Attributes;
use TotalRatingVendors\TotalSuite\Foundation\Helpers\Html;

/**
 * Class Form
 *
 * @package TotalSuite\Foundation
 */
class Form implements Arrayable, ArrayAccess, HTMLRenderable
{
    use Types, Attributes;

    /**
     * @var Engine|mixed|null
     */
    protected static $renderer;

    /**
     * @var Field[]
     */
    protected $fields = [];

    /**
     * Form constructor.
     *
     * @param array $attributes
     * @param Field[] $fields
     */
    public function __construct(array $attributes = [], array $fields = [])
    {
        $this->attributes = $attributes;
        $this->fields = $fields;
    }

    /**
     * @param Engine $engine
     */
    public static function setRenderer(Engine $engine)
    {
        static::$renderer = $engine;
    }

    /**
     * @param array $attributes
     * @param array $fields
     *
     * @return static
     * @throws Exception
     */
    public static function create(array $attributes = [], array $fields = [])
    {
        $form = new static($attributes, $fields);

        foreach ($fields as $name => $field) {
            $type = $field['type'];

            if (!$typeClass = static::getFieldType($type)) {
                throw new Exception('Invalid field type provided');
            }

            $form->addField($typeClass::fromArray($field));
        }

        return $form;
    }

    /**
     * @param Field $field
     *
     * @return Form
     */
    public function addField(Field $field)
    {
        $this->fields[$field->getName()] = $field;
        return $this;
    }

    /**
     * @param $name
     *
     * @return Field|null
     */
    public function getField($name)
    {
        return $this->fields[$name] ?? null;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function hasField($name)
    {
        return isset($this->fields[$name]);
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        /**
         * @var Field[]
         */
        $fields = [];

        foreach ($this->fields as $name => $field) {
            $fields[$name] = $field->toArray();
        }

        return ['attributes' => $this->attributes, 'fields' => $fields];
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return isset($this->fields[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->fields[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        $this->fields[$offset] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        unset($this->fields[$offset]);
    }

    /**
     * @return Html
     */
    public function toHTML()
    {
        $form = Html::create('form', $this->attributes);

        foreach ($this->fields as $name => $field) {
            $form->addContent($field->toHTML());
        }

        return $form;
    }
}