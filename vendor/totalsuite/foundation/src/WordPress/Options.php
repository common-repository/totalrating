<?php

namespace TotalRatingVendors\TotalSuite\Foundation\WordPress;
! defined( 'ABSPATH' ) && exit();


use InvalidArgumentException;
use TotalRatingVendors\TotalSuite\Foundation\Helpers\Concerns\ResolveFromContainer;
use TotalRatingVendors\TotalSuite\Foundation\Support\Collection;

/**
 * Class Options
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\WordPress
 */
class Options extends Collection
{
    use ResolveFromContainer;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var array
     */
    protected $defaults;

    /**
     * @var string
     */
    protected $originalSignature;

    /**
     * Options constructor.
     *
     * @param string $key
     * @param array $defaults
     */
    public function __construct(string $key, $defaults = [])
    {
        parent::__construct([]);

        if (!is_string($key) || trim($key) === '') {
            throw new InvalidArgumentException('Invalid argument, option key must be a string');
        }

        $this->key = $key;
        $this->defaults = $defaults;
        $this->refresh();
    }

    /**
     * Get options from database.
     *
     * @return void
     */
    public function refresh()
    {
        $stored = (array)json_decode(get_option($this->key, '{}'), true);
        $this->fill(array_replace_recursive($this->defaults, $stored));
        $this->originalSignature = $this->getSignature();
    }

    /**
     * Save the options to WordPress.
     *
     * @return bool
     */
    public function save()
    {
        if ($this->originalSignature === $this->getSignature()) {
            return true;
        }

        return update_option($this->key, json_encode($this->all()));
    }

    /**
     * @param $key
     *
     * @param array $defaults
     * @return $this
     */
    public function withKey($key, $defaults = []): self
    {
        return new static($key, $defaults);
    }

    /**
     * @return Collection
     */
    public function getDefaults(): Collection
    {
        return Collection::create($this->defaults);
    }

    /**
     * @param array $defaults
     */
    public function setDefaults(array $defaults)
    {
        $this->defaults = $defaults;
    }

    /**
     * @return string
     */
    public function getOriginalSignature(): string
    {
        return $this->originalSignature;
    }
}
