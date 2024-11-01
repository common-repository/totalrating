<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Support;
! defined( 'ABSPATH' ) && exit();


use ArrayAccess;
use ArrayIterator;
use Closure;
use Countable;
use IteratorAggregate;
use TotalRatingVendors\TotalSuite\Foundation\Contracts\Support\Arrayable;
use TotalRatingVendors\TotalSuite\Foundation\Http\Concerns\WithJsonResponse;

/**
 * Class Collection
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Support
 */
class Collection implements ArrayAccess, Arrayable, Countable, IteratorAggregate
{
    use WithJsonResponse;

    /**
     * The items contained in the collection.
     *
     * @var array
     */
    protected $items = [];

    /**
     * Create a new collection.
     *
     * @param mixed $items
     *
     * @return void
     */
    public function __construct($items = [])
    {
        $this->fill($items);
    }

    /**
     * Return a fresh collection.
     *
     * @param array $items
     * @return static
     */
    protected function fresh($items = [])
    {
        return new static($items);
    }

    /**
     * @param array $array
     *
     * @return static
     */
    public static function create(array $array = [])
    {
        return new static($array);
    }

    /**
     * Calculate the signature (hash) of the collection.
     * @return string
     */
    public function getSignature(): string
    {
        return sha1(serialize($this->items));
    }

    /**
     * Get base collection.
     *
     * @return $this
     */
    public function getBase(): self
    {
        return new self($this->all());
    }

    /**
     * @return array
     */
    public function flatten()
    {
        return Arrays::flatten($this->items);
    }

    /**
     * @param array $items
     * @return Collection
     */
    public function fill(array $items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * Determine if an item exists in the collection.
     *
     * @param mixed $key
     * @param mixed $operator
     * @param mixed $value
     *
     * @return bool
     */
    public function contains($key, $operator = null, $value = null): bool
    {
        if (func_num_args() === 1) {
            if (is_callable($key)) {
                foreach ($this->items as $itemKey => $itemValue) {
                    if ($key($itemValue, $itemKey)) {
                        return true;
                    }
                }

                return false;
            }

            return Arrays::find($this->items, $key);
        }

        return $this->contains($this->applyOperator(...func_get_args()));
    }

    /**
     * @param      $key
     * @param null $operator
     * @param null $value
     *
     * @return Closure
     */
    protected function applyOperator($key, $operator = null, $value = null): callable
    {
        if (func_num_args() === 1) {
            $value = true;
            $operator = '=';
        }

        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        return static function ($item) use ($key, $operator, $value) {
            $data = is_array($item) ? Arrays::get($item, $key) : $item;

            $strings = array_filter(
                [$data, $value],
                static function ($value) {
                    return is_string($value) || (is_object($value) && method_exists($value, '__toString'));
                }
            );

            if (count($strings) < 2 && count(array_filter([$data, $value], 'is_object')) === true) {
                return in_array($operator, ['!=', '<>', '!==']);
            }

            switch ($operator) {
                default:
                case '=':
                case '==':
                    return $data == $value;
                case '!=':
                case '<>':
                    return $data != $value;
                case '<':
                    return $data < $value;
                case '>':
                    return $data > $value;
                case '<=':
                    return $data <= $value;
                case '>=':
                    return $data >= $value;
                case '===':
                    return $data === $value;
                case '!==':
                    return $data !== $value;
            }
        };
    }

    /**
     * Get all items except for those with the specified keys.
     *
     * @param array $keys
     *
     * @return static
     */
    public function except($keys): Collection
    {
        if (!is_array($keys)) {
            $keys = func_get_args();
        }

        return $this->fresh(Arrays::except($this->items, $keys));
    }

    /**
     * Remove an item from the collection by key.
     *
     * @param string|array $keys
     *
     * @return $this
     */
    public function forget($keys): self
    {
        foreach ((array)$keys as $key) {
            $this->remove($key);
        }

        return $this;
    }

    /**
     * Get an item from the collection by key.
     *
     * @param mixed $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arrays::get($this->items, $key, $default);
    }

    /**
     * @param $key
     * @return $this
     */
    public function remove($key)
    {
        Arrays::delete($this->items, $key);

        return $this;
    }

    /**
     * Unset the item at a given offset.
     *
     * @param string $key
     *
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($key)
    {
        $this->remove($key);
    }

    /**
     * @param callable|null $callback
     * @param null $default
     *
     * @return mixed|null
     */
    public function first(callable $callback = null, $default = null)
    {
        return Arrays::first($this->items, $callback, $default);
    }

    /**
     * Determine if an item exists in the collection by key.
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function has($key): bool
    {
        $keys = is_array($key) ? $key : func_get_args();
        foreach ($keys as $value) {
            if (!Arrays::has($this->items, $value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if the collection is empty or not.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    /**
     * Get the keys of the collection items.
     *
     * @return static
     */
    public function keys(): Collection
    {
        return $this->fresh(array_keys($this->items));
    }

    /**
     * Get the values of a given key and delete the key.
     *
     * @param string|array $value
     * @param string|null $key
     *
     * @return static
     */
    public function extract($value, $key = null): Collection
    {
        return $this->fresh(Arrays::extract($this->items, $value, $key));
    }

    /**
     * Run an associative map over each of the items.
     *
     * The callback should return an associative array with a single key/value pair.
     *
     * @param callable $callback
     *
     * @return static
     */
    public function mapWithKeys(callable $callback): Collection
    {
        $result = [];
        foreach ($this->items as $key => $value) {
            $assoc = $callback($value, $key);
            foreach ($assoc as $mapKey => $mapValue) {
                $result[$mapKey] = $mapValue;
            }
        }
        return $this->fresh($result);
    }

    /**
     * Merge the collection with the given items.
     *
     * @param mixed $items
     *
     * @return static
     */
    public function merge($items): Collection
    {
        return $this->fresh(array_merge($this->items, $items));
    }

    /**
     * Recursively merge the collection with the given items.
     *
     * @param mixed $items
     *
     * @return static
     */
    public function mergeRecursive($items): Collection
    {
        return $this->fresh(array_merge_recursive($this->items, $items));
    }

    /**
     * Create a collection by using this collection for keys and another for its values.
     *
     * @param mixed $values
     *
     * @return static
     */
    public function combine($values): Collection
    {
        return $this->fresh(array_combine($this->all(), $values));
    }

    /**
     * Get all the items in the collection.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Union the collection with the given items.
     *
     * @param mixed $items
     *
     * @return static
     */
    public function union($items): Collection
    {
        return $this->fresh($this->items + $items);
    }

    /**
     * Get the items with the specified keys.
     *
     * @param mixed $keys
     *
     * @return static
     */
    public function only($keys): Collection
    {
        if ($keys === null) {
            return $this->fresh($this->items);
        }

        return $this->fresh(Arrays::only($this->items, $keys));
    }

    /**
     * Get and remove the last item from the collection.
     *
     * @return mixed
     */
    public function pop()
    {
        return array_pop($this->items);
    }

    /**
     * Push an item onto the beginning of the collection.
     *
     * @param mixed $value
     * @param mixed $key
     *
     * @return $this
     */
    public function prepend($value, $key = null): self
    {
        $this->items = Arrays::prepend($this->items, $value, $key);
        return $this;
    }

    /**
     * Push all of the given items onto the collection.
     *
     * @param iterable $source
     *
     * @return static
     */
    public function concat($source): Collection
    {
        $result = $this->fresh($this);

        foreach ($source as $item) {
            $result->push($item);
        }

        return $result;
    }

    /**
     * Push an item onto the end of the collection.
     *
     * @param mixed $value
     *
     * @return $this
     */
    public function push($value): Collection
    {
        $this->items[] = $value;
        return $this;
    }

    /**
     * Get and remove an item from the collection.
     *
     * @param mixed $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function pull($key, $default = null)
    {
        return Arrays::pull($this->items, $key, $default);
    }

    /**
     * Reduce the collection to a single value.
     *
     * @param callable $callback
     * @param mixed $initial
     *
     * @return mixed
     */
    public function reduce(callable $callback, $initial = null)
    {
        return array_reduce($this->items, $callback, $initial);
    }

    /**
     * Replace the collection items with the given items.
     *
     * @param mixed $items
     *
     * @return static
     */
    public function replace($items): Collection
    {
        return $this->fresh(array_replace($this->items, $items));
    }

    /**
     * Recursively replace the collection items with the given items.
     *
     * @param mixed $items
     *
     * @return static
     */
    public function replaceRecursive($items): Collection
    {
        return $this->fresh(array_replace_recursive($this->items, $items));
    }

    /**
     * Reverse items order.
     *
     * @return static
     */
    public function reverse(): Collection
    {
        return $this->fresh(array_reverse($this->items, true));
    }

    /**
     * Search the collection for a given value and return the corresponding key if successful.
     *
     * @param mixed $value
     * @param bool $strict
     *
     * @return mixed
     */
    public function search($value, $strict = false)
    {
        if (!is_callable($value)) {
            return array_search($value, $this->items, $strict);
        }

        foreach ($this->items as $key => $item) {
            if ($value($item, $key)) {
                return $key;
            }
        }
        return false;
    }

    /**
     * Get and remove the first item from the collection.
     *
     * @return mixed
     */
    public function shift()
    {
        return array_shift($this->items);
    }

    /**
     * Shuffle the items in the collection.
     *
     * @return static
     */
    public function shuffle(): Collection
    {
        $items = $this->items;
        return $this->fresh(shuffle($items));
    }

    /**
     * Skip the first {$count} items.
     *
     * @param int $count
     *
     * @return static
     */
    public function skip($count): Collection
    {
        return $this->slice($count);
    }

    /**
     * Slice the underlying collection array.
     *
     * @param int $offset
     * @param int $length
     *
     * @return static
     */
    public function slice($offset, $length = null): Collection
    {
        return $this->fresh(array_slice($this->items, $offset, $length, true));
    }

    /**
     * Chunk the collection into chunks of the given size.
     *
     * @param int $size
     *
     * @return static
     */
    public function chunk($size): Collection
    {
        if ($size <= 0) {
            return $this->fresh();
        }
        $chunks = [];
        foreach (array_chunk($this->items, $size, true) as $chunk) {
            $chunks[] = $this->fresh($chunk);
        }

        return $this->fresh($chunks);
    }

    /**
     * Sort through each item with a callback.
     *
     * @param callable|null $callback
     *
     * @return static
     */
    public function sort(callable $callback = null): Collection
    {
        $items = $this->items;
        $callback ? uasort($items, $callback) : asort($items);
        return $this->fresh($items);
    }

    /**
     * Sort the collection in descending order using the given callback.
     *
     * @param callable|string $callback
     * @param int $options
     *
     * @return static
     */
    public function sortByDesc($callback, $options = SORT_REGULAR): Collection
    {
        return $this->sortBy($callback, $options, true);
    }

    /**
     * Sort the collection using the given callback.
     *
     * @param callable|string $callback
     * @param int $options
     * @param bool $descending
     *
     * @return static
     */
    public function sortBy($callback, $options = SORT_REGULAR, $descending = false): Collection
    {
        $results = [];
        $callback = is_callable($callback) ? $callback : static function ($key) use ($callback) {
            Arrays::get($key, $callback);
        };

        // First we will loop through the items and get the comparator from a callback
        // function which we were given. Then, we will sort the returned values and
        // and grab the corresponding values for the sorted keys from this array.
        foreach ($this->items as $key => $value) {
            $results[$key] = $callback($value, $key);
        }
        $descending ? arsort($results, $options) : asort($results, $options);
        // Once we have sorted all of the keys in the array, we will loop through them
        // and grab the corresponding model so we can set the underlying items list
        // to the sorted version. Then we'll just return the collection instance.
        foreach (array_keys($results) as $key) {
            $results[$key] = $this->items[$key];
        }
        return $this->fresh($results);
    }

    /**
     * Sort the collection keys in descending order.
     *
     * @param int $options
     *
     * @return static
     */
    public function sortKeysDesc($options = SORT_REGULAR): Collection
    {
        return $this->sortKeys($options, true);
    }

    /**
     * Sort the collection keys.
     *
     * @param int $options
     * @param bool $descending
     *
     * @return static
     */
    public function sortKeys($options = SORT_REGULAR, $descending = false): Collection
    {
        $items = $this->items;
        $descending ? krsort($items, $options) : ksort($items, $options);
        return $this->fresh($items);
    }

    /**
     * Splice a portion of the underlying collection array.
     *
     * @param int $offset
     * @param int|null $length
     * @param mixed $replacement
     *
     * @return static
     */
    public function splice($offset, $length = null, $replacement = []): Collection
    {
        if (func_num_args() === 1) {
            return $this->fresh(array_splice($this->items, $offset));
        }
        return $this->fresh(array_splice($this->items, $offset, $length, $replacement));
    }

    /**
     * Take the first or last {$limit} items.
     *
     * @param int $limit
     *
     * @return static
     */
    public function take($limit): Collection
    {
        if ($limit < 0) {
            return $this->slice($limit, abs($limit));
        }
        return $this->slice(0, $limit);
    }

    /**
     * Transform each item in the collection using a callback.
     *
     * @param callable $callback
     *
     * @return $this
     */
    public function transform(callable $callback): self
    {
        $this->items = $this->map($callback)->all();
        return $this;
    }

    /**
     * Run a map over each of the items.
     *
     * @param callable $callback
     *
     * @return static
     */
    public function map(callable $callback): Collection
    {
        $keys = array_keys($this->items);
        $items = array_map($callback, $this->items, $keys);
        return $this->fresh(array_combine($keys, $items));
    }

    /**
     * Reset the keys on the underlying array.
     *
     * @return static
     */
    public function values(): Collection
    {
        return $this->fresh(array_values($this->items));
    }

    /**
     * Pad collection to the specified length with a value.
     *
     * @param int $size
     * @param mixed $value
     *
     * @return static
     */
    public function pad($size, $value): Collection
    {
        return $this->fresh(array_pad($this->items, $size, $value));
    }

    /**
     * Add an item to the collection.
     *
     * @param mixed $item
     *
     * @return $this
     */
    public function add($item): self
    {
        $this->items[] = $item;
        return $this;
    }

    /**
     * @param array|callable $where
     *
     * @return Collection
     */
    public function where($where): Collection
    {
        return $this->fresh(Arrays::where($this->items, $where));
    }

    // Array functions wrappers

    /**
     * Run a filter over each of the items.
     *
     * @param callable|null $callback
     *
     * @return static
     */
    public function filter(callable $callback = null): Collection
    {
        if ($callback === null) {
            $items = array_filter($this->items);
        } else {
            $items = array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH);
        }
        return $this->fresh($items);
    }

    /**
     * Flip the items in the collection.
     *
     * @return static
     */
    public function flip(): Collection
    {
        return $this->fresh(array_flip($this->items));
    }

    /**
     * Concatenate values of a given key as a string.
     *
     * @param string $glue
     *
     * @return string
     */
    public function implode($glue = null): string
    {
        return implode($glue, $this->items);
    }

    /**
     * Intersect the collection with the given items.
     *
     * @param mixed $items
     *
     * @return static
     */
    public function intersect($items): Collection
    {
        return $this->fresh(array_intersect($this->items, $items));
    }

    /**
     * Intersect the collection with the given items by key.
     *
     * @param mixed $items
     *
     * @return static
     */
    public function intersectByKeys($items): Collection
    {
        return $this->fresh(
            array_intersect_key(
                $this->items,
                $items
            )
        );
    }

    /**
     * Get the items in the collection that are not present in the given items.
     *
     * @param mixed $items
     *
     * @return static
     */
    public function diff($items): Collection
    {
        return $this->fresh(array_diff($this->items, $items));
    }

    /**
     * Get the items in the collection that are not present in the given items, using the callback.
     *
     * @param mixed $items
     * @param callable $callback
     *
     * @return static
     */
    public function diffUsing($items, callable $callback): Collection
    {
        return $this->fresh(array_udiff($this->items, $items, $callback));
    }

    /**
     * Get the items in the collection whose keys and values are not present in the given items.
     *
     * @param mixed $items
     *
     * @return static
     */
    public function diffAssoc($items): Collection
    {
        return $this->fresh(array_diff_assoc($this->items, $items));
    }

    /**
     * Get the items in the collection whose keys and values are not present in the given items, using the callback.
     *
     * @param mixed $items
     * @param callable $callback
     *
     * @return static
     */
    public function diffAssocUsing($items, callable $callback): Collection
    {
        return $this->fresh(array_diff_uassoc($this->items, $items, $callback));
    }

    /**
     * Get the items in the collection whose keys are not present in the given items.
     *
     * @param mixed $items
     *
     * @return static
     */
    public function diffKeys($items): Collection
    {
        return $this->fresh(array_diff_key($this->items, $items));
    }

    /**
     * Get the items in the collection whose keys are not present in the given items, using the callback.
     *
     * @param mixed $items
     * @param callable $callback
     *
     * @return static
     */
    public function diffKeysUsing($items, callable $callback): Collection
    {
        return $this->fresh(array_diff_ukey($this->items, $items, $callback));
    }

    // Interfaces implementations

    /**
     * Count the number of items in the collection.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Determine if an item exists at an offset.
     *
     * @param mixed $key
     *
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($key): bool
    {
        return $this->has($key);
    }

    /**
     * Get an item at a given offset.
     *
     * @param mixed $key
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Set the item at a given offset.
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * @param mixed $key
     * @param mixed $value
     *
     * @return $this
     */
    public function set($key, $value)
    {
        if ($key === null) {
            $this->push($value);
        } else {
            Arrays::set($this->items, $key, $value);
        }

        return $this;
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
        return $this->all();
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }
}