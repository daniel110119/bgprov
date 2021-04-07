<?php


namespace Bgprov\Kernel\Support;


/**
 * Class Arr
 * @package Bgprov\Kernel\Support
 */
class Arr
{
    /**
     * 如果元素不存在，则使用“点”符号将其添加到数组中。
     *
     * @param array  $array
     * @param string $key
     * @param mixed  $value
     *
     * @return array
     */
    public static function add(array $array, $key, $value)
    {
        if (is_null(static::get($array, $key))) {
            static::set($array, $key, $value);
        }

        return $array;
    }

    /**
     * 交叉连接给定的数组，返回所有可能的排列。
     *
     * @param array ...$arrays
     *
     * @return array
     */
    public static function crossJoin(...$arrays)
    {
        $results = [[]];

        foreach ($arrays as $index => $array) {
            $append = [];

            foreach ($results as $product) {
                foreach ($array as $item) {
                    $product[$index] = $item;

                    $append[] = $product;
                }
            }

            $results = $append;
        }

        return $results;
    }

    /**
     * 将一个数组分成两个数组。一个是键，另一个是值。
     *
     * @param array $array
     *
     * @return array
     */
    public static function divide(array $array)
    {
        return [array_keys($array), array_values($array)];
    }

    /**
     * 用点将多维关联数组平面化。
     *
     * @param array  $array
     * @param string $prepend
     *
     * @return array
     */
    public static function dot(array $array, $prepend = '')
    {
        $results = [];

        foreach ($array as $key => $value) {
            if (is_array($value) && !empty($value)) {
                $results = array_merge($results, static::dot($value, $prepend.$key.'.'));
            } else {
                $results[$prepend.$key] = $value;
            }
        }

        return $results;
    }

    /**
     * 获取除指定的项数组外的所有给定数组。
     *
     * @param array        $array
     * @param array|string $keys
     *
     * @return array
     */
    public static function except(array $array, $keys)
    {
        static::forget($array, $keys);

        return $array;
    }

    /**
     * 确定给定的键是否存在于提供的数组中。
     *
     * @param array      $array
     * @param string|int $key
     *
     * @return bool
     */
    public static function exists(array $array, $key)
    {
        return array_key_exists($key, $array);
    }

    /**
     * 返回数组中通过给定真值测试的第一个元素。
     *
     * @param array         $array
     * @param callable|null $callback
     * @param mixed         $default
     *
     * @return mixed
     */
    public static function first(array $array, callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            if (empty($array)) {
                return $default;
            }

            foreach ($array as $item) {
                return $item;
            }
        }

        foreach ($array as $key => $value) {
            if (call_user_func($callback, $value, $key)) {
                return $value;
            }
        }

        return $default;
    }

    /**
     * 返回通过给定测试的数组中的最后一个元素。
     *
     * @param array         $array
     * @param callable|null $callback
     * @param mixed         $default
     *
     * @return mixed
     */
    public static function last(array $array, callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            return empty($array) ? $default : end($array);
        }

        return static::first(array_reverse($array, true), $callback, $default);
    }

    /**
     * 将多维数组扁平化为单个级别。
     *
     * @param array $array
     * @param int   $depth
     *
     * @return array
     */
    public static function flatten(array $array, $depth = \PHP_INT_MAX)
    {
        return array_reduce($array, function ($result, $item) use ($depth) {
            $item = $item instanceof Collection ? $item->all() : $item;

            if (!is_array($item)) {
                return array_merge($result, [$item]);
            } elseif (1 === $depth) {
                return array_merge($result, array_values($item));
            }

            return array_merge($result, static::flatten($item, $depth - 1));
        }, []);
    }

    /**
     * 使用"点"符号从给定数组中删除一个或多个数组项。
     *
     * @param array        $array
     * @param array|string $keys
     */
    public static function forget(array &$array, $keys)
    {
        $original = &$array;

        $keys = (array) $keys;

        if (0 === count($keys)) {
            return;
        }

        foreach ($keys as $key) {
            // if the exact key exists in the top-level, remove it
            if (static::exists($array, $key)) {
                unset($array[$key]);

                continue;
            }

            $parts = explode('.', $key);

            // clean up before each pass
            $array = &$original;

            while (count($parts) > 1) {
                $part = array_shift($parts);

                if (isset($array[$part]) && is_array($array[$part])) {
                    $array = &$array[$part];
                } else {
                    continue 2;
                }
            }

            unset($array[array_shift($parts)]);
        }
    }

    /**
     * 使用“点”符号从数组中获取项。
     *
     * @param array  $array
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public static function get(array $array, $key, $default = null)
    {
        if (is_null($key)) {
            return $array;
        }

        if (static::exists($array, $key)) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (static::exists($array, $segment)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }

        return $array;
    }

    /**
     * 使用"点"符号检查数组中是否存在一个或多个项。
     *
     * @param array        $array
     * @param string|array $keys
     *
     * @return bool
     */
    public static function has(array $array, $keys)
    {
        if (is_null($keys)) {
            return false;
        }

        $keys = (array) $keys;

        if (empty($array)) {
            return false;
        }

        if ($keys === []) {
            return false;
        }

        foreach ($keys as $key) {
            $subKeyArray = $array;

            if (static::exists($array, $key)) {
                continue;
            }

            foreach (explode('.', $key) as $segment) {
                if (static::exists($subKeyArray, $segment)) {
                    $subKeyArray = $subKeyArray[$segment];
                } else {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * 确定数组是否具有关联性。
     *
     * 如果数组没有以0开头的连续数字键，则该数组是“关联的”。
     *
     * @param array $array
     *
     * @return bool
     */
    public static function isAssoc(array $array)
    {
        $keys = array_keys($array);

        return array_keys($keys) !== $keys;
    }

    /**
     * 从给定数组中获取项的子集。
     *
     * @param array        $array
     * @param array|string $keys
     *
     * @return array
     */
    public static function only(array $array, $keys)
    {
        return array_intersect_key($array, array_flip((array) $keys));
    }

    /**
     * Push an item onto the beginning of an array.
     *
     * @param array $array
     * @param mixed $value
     * @param mixed $key
     *
     * @return array
     */
    public static function prepend(array $array, $value, $key = null)
    {
        if (is_null($key)) {
            array_unshift($array, $value);
        } else {
            $array = [$key => $value] + $array;
        }

        return $array;
    }

    /**
     * 从数组中获取一个值，并删除它。
     *
     * @param array  $array
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public static function pull(array &$array, $key, $default = null)
    {
        $value = static::get($array, $key, $default);

        static::forget($array, $key);

        return $value;
    }

    /**
     * Get a 1 value from an array.
     *
     * @param array    $array
     * @param int|null $amount
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public static function random(array $array, int $amount = null)
    {
        if (is_null($amount)) {
            return $array[array_rand($array)];
        }

        $keys = array_rand($array, $amount);

        $results = [];

        foreach ((array) $keys as $key) {
            $results[] = $array[$key];
        }

        return $results;
    }

    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param array  $array
     * @param string $key
     * @param mixed  $value
     *
     * @return array
     */
    public static function set(array &$array, string $key, $value)
    {
        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }
    public static function accessible($value)
    {
        return is_array($value) || $value instanceof \ArrayAccess;
    }
    /**
     * Filter the array using the given callback.
     *
     * @param array    $array
     * @param callable $callback
     *
     * @return array
     */
    public static function where(array $array, callable $callback)
    {
        return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * If the given value is not an array, wrap it in one.
     *
     * @param mixed $value
     *
     * @return array
     */
    public static function wrap($value)
    {
        return !is_array($value) ? [$value] : $value;
    }
}