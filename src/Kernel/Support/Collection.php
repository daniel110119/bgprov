<?php


namespace Bgprov\Kernel\Support;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Exception;
use IteratorAggregate;
use JsonSerializable;
use Serializable;
use Traversable;

/**
 * Class Collection
 * @package Bgprov\Kernel\Support
 */
class Collection implements ArrayAccess, Countable, IteratorAggregate, JsonSerializable, Serializable
{

    protected $items = [];

    public function __construct(array $items = [])
    {
        foreach ($items as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Filter items by the given key value pair.
     *
     * @param  string  $key
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return static
     */
    public function where($key, $operator = null, $value = null)
    {
        return $this->filter($this->operatorForWhere(...func_get_args()));
    }

    /**
     * Execute a callback over each item.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function each(callable $callback)
    {
        foreach ($this as $key => $item) {
            if ($callback($item, $key) === false) {
                break;
            }
        }

        return $this;
    }
    public function map(callable $callback)
    {
        return array_map($callback,$this->toArray());
    }
    /**
     * Get an operator checker callback.
     *
     * @param  string  $key
     * @param  string|null  $operator
     * @param  mixed  $value
     * @return \Closure
     */
    protected function operatorForWhere($key, $operator = null, $value = null)
    {
        if (func_num_args() === 1) {
            $value = true;

            $operator = '=';
        }

        if (func_num_args() === 2) {
            $value = $operator;

            $operator = '=';
        }

        return function ($item) use ($key, $operator, $value) {
            $retrieved = data_get($item, $key);

            $strings = array_filter([$retrieved, $value], function ($value) {
                return is_string($value) || (is_object($value) && method_exists($value, '__toString'));
            });

            if (count($strings) < 2 && count(array_filter([$retrieved, $value], 'is_object')) == 1) {
                return in_array($operator, ['!=', '<>', '!==']);
            }

            switch ($operator) {
                default:
                case '=':
                case '==':  return $retrieved == $value;
                case '!=':
                case '<>':  return $retrieved != $value;
                case '<':   return $retrieved < $value;
                case '>':   return $retrieved > $value;
                case '<=':  return $retrieved <= $value;
                case '>=':  return $retrieved >= $value;
                case '===': return $retrieved === $value;
                case '!==': return $retrieved !== $value;
            }
        };
    }
    /**
     * Run a filter over each of the items.
     *
     * @param  callable|null  $callback
     * @return static
     */
    public function filter(callable $callback = null)
    {
        if ($callback) {
            return new static(Arr::where($this->items, $callback));
        }

        return new static(array_filter($this->items));
    }
    /**
     * @param array $keys
     * @return $this
     */
    public function only(array $keys)
    {
        $return = [];

        foreach ($keys as $key) {
            $value = $this->get($key);

            if (!is_null($value)) {
                $return[$key] = $value;
            }
        }

        return new static($return);
    }

    /**
     * @param $keys
     * @return $this
     */
    public function except($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();

        return new static(Arr::except($this->items, $keys));
    }

    /**
     * @param $items
     * @return $this
     */
    public function merge($items)
    {
        $clone = new static($this->all());

        foreach ($items as $key => $value) {
            $clone->set($key, $value);
        }

        return $clone;
    }

    /**
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        return !is_null(Arr::get($this->items, $key));
    }

    /**
     * @return mixed
     */
    public function first()
    {
        return reset($this->items);
    }

    /**
     * @return mixed
     */
    public function last()
    {
        $end = end($this->items);

        reset($this->items);

        return $end;
    }

    /**
     * @param $key
     * @param $value
     */
    public function add($key, $value)
    {
        Arr::set($this->items, $key, $value);
    }

    /**
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        Arr::set($this->items, $key, $value);
    }

    /**
     * @param $key
     * @param null $default
     * @return array|mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->items, $key, $default);
    }

    /**
     * @param $key
     */
    public function forget($key)
    {
        Arr::forget($this->items, $key);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->all();
    }

    /**
     * @param int $option
     * @return false|string
     */
    public function toJson($option = JSON_UNESCAPED_UNICODE)
    {
        return json_encode($this->all(), $option);
    }

    /**
     * @return false|string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return $this->items;
    }

    /**
     * @return string|null
     */
    public function serialize()
    {
        return serialize($this->items);
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * @param string $serialized
     * @return mixed|void
     */
    public function unserialize($serialized)
    {
        return $this->items = unserialize($serialized);
    }

    public function __get($key)
    {
        return $this->get($key);
    }

    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    public function __isset($key)
    {
        return $this->has($key);
    }

    public function __unset($key)
    {
        $this->forget($key);
    }

    public function __set_state()
    {
        return $this->all();
    }

    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            $this->forget($offset);
        }
    }

    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->get($offset) : null;
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

}