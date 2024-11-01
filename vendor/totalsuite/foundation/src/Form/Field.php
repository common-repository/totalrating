<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Form;
! defined( 'ABSPATH' ) && exit();


use Illuminate\Contracts\Support\Arrayable;
use TotalRatingVendors\TotalSuite\Foundation\Contracts\Support\HTMLRenderable;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Helpers\Concerns\Attributes;
use TotalRatingVendors\TotalSuite\Foundation\Support\Arrays;
use TotalRatingVendors\TotalSuite\Foundation\Support\Collection;

/**
 * Class Field
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Form
 */
abstract class Field implements Arrayable, HTMLRenderable
{
    use Attributes;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var mixed
     */
    protected $defaultValue;

    /**
     * @var array
     */
    protected $label = [];

    /**
     * @var Collection
     */
    protected $errors;

    /**
     * @var string
     */
    protected $help = '';

    /**
     * Field constructor.
     *
     * @param string $name
     * @param array  $attributes
     * @param null   $value
     */
    public function __construct(string $name, array $attributes = [], $value = null)
    {
        $this->name       = $name;
        $this->attributes = array_merge($this->attributes, $attributes);
        $this->value      = $value;
        $this->errors     = new Collection();
    }

    /**
     * @param array $field
     *
     * @return Field
     * @throws Exception
     */
    public static function fromArray(array $field)
    {
        if (empty($field['name'])) {
            throw new Exception('Field name missing');
        }

        $field = Arrays::merge(
            $field,
            [
                'attributes'   => [],
                'value'        => null,
                'defaultValue' => null,
                'help'         => '',
                'label'        => [],
                'errors'       => [],
            ]
        );

        $instance = new static($field['name'], $field['attributes'], $field['value']);
        $instance->setDefaultValue($field['defaultValue'])->setLabel($field['label'])->setHelp(
                $field['help']
            )->setErrors($field['errors']);

        return $instance;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return Collection
     */
    public function getErrors(): Collection
    {
        return $this->errors;
    }

    /**
     * @param array $errors
     *
     * @return Field
     */
    public function setErrors(array $errors): Field
    {
        $this->errors->fill($errors);
        return $this;
    }

    /**
     * @param array $exclude
     *
     * @return array
     */
    public function getHtmlAttributes(array $exclude = []): array
    {
        $attributes = array_merge(
            [
                'name'  => $this->getName(),
                'value' => $this->getValue(),
            ],
            Arrays::except($this->getAttributes(), ['name', 'value'])
        );

        $attributes = Arrays::except($attributes, $exclude);

        return $attributes;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value ?? $this->defaultValue;
    }

    /**
     * @param mixed $value
     *
     * @return Field
     */
    public function setValue($value): Field
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'attributes'   => $this->getAttributes(),
            'name'         => $this->getName(),
            'value'        => $this->getValue(),
            'help'         => $this->getHelp(),
            'errors'       => $this->errors->all(),
            'defaultValue' => $this->getDefaultValue(),
            'label'        => $this->getLabel(),
        ];
    }

    /**
     * @return string
     */
    public function getHelp(): string
    {
        return $this->help;
    }

    /**
     * @param string $help
     *
     * @return Field
     */
    public function setHelp(string $help): Field
    {
        $this->help = $help;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @param mixed $defaultValue
     *
     * @return Field
     */
    public function setDefaultValue($defaultValue): Field
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * @return array
     */
    public function getLabel(): array
    {
        return $this->label;
    }

    /**
     * @param string $content
     * @param array  $attributes
     *
     * @return Field
     */
    public function setLabel(string $content, array $attributes = []): Field
    {
        $this->label = compact('content', 'attributes');

        return $this;
    }

}
