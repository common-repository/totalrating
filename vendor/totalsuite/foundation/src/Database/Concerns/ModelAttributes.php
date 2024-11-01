<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Database\Concerns;
! defined( 'ABSPATH' ) && exit();



use TotalRatingVendors\TotalSuite\Foundation\Support\Arrays;

trait ModelAttributes
{
    /**
     * @var array
     */
    protected $types = [];

    /**
     * @var array
     */
    protected $fillable = [];

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @param array $attributes
     *
     * @return array
     */
    public function filterAttributes(array $attributes)
    {
        return Arrays::only($attributes, $this->fillable);
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param $attributes
     *
     * @return array
     */
    public function fillAttributes($attributes)
    {
        return $this->setRawAttributes($this->filterAttributes($attributes));
    }

    /**
     * @param $attributes
     *
     * @return array
     */
    public function fillAndCastAttributes($attributes)
    {
        return $this->setAttributes($this->filterAttributes($attributes));
    }

    /**
     * @param $attributes
     *
     * @return array
     */
    public function setAttributes($attributes)
    {
        return $this->attributes = $this->typeAttributes($attributes);
    }

    /**
     * @param $attributes
     *
     * @return array
     */
    public function setRawAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @param string $name
     */
    public function deleteAttribute($name)
    {
        Arrays::delete($this->attributes, $name);
    }

    /**
     * @param $name
     *
     * @return mixed|null
     */
    public function __get($name)
    {
        return $this->getAttribute($name);
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->setAttribute($name, $value);
    }

    /**
     * @param      $name
     * @param null $default
     *
     * @return null
     */
    public function getAttribute($name, $default = null)
    {
        return Arrays::get($this->attributes, $name, $default);
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setAttribute($name, $value)
    {
        Arrays::set($this->attributes, $name, $value);
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return $this->hasAttribute($name);
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function hasAttribute($name)
    {
        return Arrays::has($this->attributes, $name);
    }

    /**
     * @param array $values
     *
     * @return array
     */
    protected function typeAttributes(array $values): array
    {
        if (empty($this->types)) {
            return $values;
        }

        foreach ($this->types as $attribute => $type) {
            $value = $values[$attribute] ?? null;

            if ($value === null) {
                continue;
            }

            switch ($type) {
                case 'object' :
                {
                    $values[$attribute] = is_object($value) ? $value : json_decode($value);
                    break;
                }
                case 'array' :
                {
                    $values[$attribute] = is_array($value) ? $value : json_decode($value, true);
                    break;
                }
                case 'integer' :
                case 'int' :
                {
                    $values[$attribute] = (int)$value;
                    break;
                }
                case 'double' :
                case 'float' :
                {
                    $values[$attribute] = (float)$value;
                    break;
                }
                case 'string' :
                {
                    $values[$attribute] = (string)$value;
                    break;
                }
                case 'bool' :
                case 'boolean' :
                {
                    $values[$attribute] = (bool)$value;
                    break;
                }
                default :
                {
                    $values[$attribute] = $this->applyCustomCast($type, $value);
                }
            }
        }

        return $values;
    }

    /**
     * @param $type
     * @param $value
     *
     * @return bool
     */
    public function applyCustomCast($type, $value)
    {
        if (!is_string($type) || empty($type)) {
            return false;
        }

        $method = 'castTo' . ucfirst($type);

        if (method_exists($this, $method)) {
            return $this->$method($value);
        }

        return false;
    }

    /**
     * Transform attributes to database
     *
     * @return array
     */
    protected function getFormattedAttributesForDatabase(): array
    {
        $attributes = [];

        foreach ($this->attributes as $key => $value) {
            if (is_array($value) || is_object($value)) {
                $attributes[$key] = json_encode($value);
            } else {
                $attributes[$key] = $value;
            }
        }

        return $attributes;
    }
}
