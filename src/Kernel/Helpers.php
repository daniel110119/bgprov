<?php
if (!function_exists('getRootPath')) {
    function getRootPath()
    {
        $reflection = new \ReflectionClass(\Composer\Autoload\ClassLoader::class);
        $vendorDir = dirname(dirname($reflection->getFileName()), 3);
        return $vendorDir;
    }
}

if (!function_exists('dd')) {
    function dd($data)
    {
        var_dump($data);
        dd();
    }
}


if (!function_exists('dump')) {
    function dump($data)
    {
        var_dump($data);
    }
}

if (!function_exists('collect')) {
    function collect(array $arr)
    {
        return new \Bgprov\Kernel\Support\Collection($arr);
    }
}


if (!function_exists('data_set')) {
    /**
     * Set an item on an array or object using dot notation.
     *
     * @param mixed $target
     * @param string|array $key
     * @param mixed $value
     * @param bool $overwrite
     * @return mixed
     */
    function data_set(&$target, $key, $value, $overwrite = true)
    {
        $segments = is_array($key) ? $key : explode('.', $key);

        if (($segment = array_shift($segments)) === '*') {
            if (!\Bgprov\Kernel\Support\Arr::accessible($target)) {
                $target = [];
            }

            if ($segments) {
                foreach ($target as &$inner) {
                    data_set($inner, $segments, $value, $overwrite);
                }
            } elseif ($overwrite) {
                foreach ($target as &$inner) {
                    $inner = $value;
                }
            }
        } elseif (\Bgprov\Kernel\Support\Arr::accessible($target)) {
            if ($segments) {
                if (!\Bgprov\Kernel\Support\Arr::exists($target, $segment)) {
                    $target[$segment] = [];
                }

                data_set($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite || !\Bgprov\Kernel\Support\Arr::exists($target, $segment)) {
                $target[$segment] = $value;
            }
        } elseif (is_object($target)) {
            if ($segments) {
                if (!isset($target->{$segment})) {
                    $target->{$segment} = [];
                }

                data_set($target->{$segment}, $segments, $value, $overwrite);
            } elseif ($overwrite || !isset($target->{$segment})) {
                $target->{$segment} = $value;
            }
        } else {
            $target = [];

            if ($segments) {
                data_set($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite) {
                $target[$segment] = $value;
            }
        }

        return $target;
    }
}
if (! function_exists('data_get')) {
    /**
     * Get an item from an array or object using "dot" notation.
     *
     * @param  mixed  $target
     * @param  string|array|int|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    function data_get($target, $key, $default = null)
    {
        if (is_null($key)) {
            return $target;
        }

        $key = is_array($key) ? $key : explode('.', $key);

        foreach ($key as $i => $segment) {
            unset($key[$i]);

            if (is_null($segment)) {
                return $target;
            }

            if ($segment === '*') {
                if ($target instanceof \Bgprov\Kernel\Support\Collection) {
                    $target = $target->all();
                } elseif (! is_array($target)) {
                    return value($default);
                }

                $result = [];

                foreach ($target as $item) {
                    $result[] = data_get($item, $key);
                }

                return in_array('*', $key) ? \Bgprov\Kernel\Support\Arr::collapse($result) : $result;
            }

            if (\Bgprov\Kernel\Support\Arr::accessible($target) && \Bgprov\Kernel\Support\Arr::exists($target, $segment)) {
                $target = $target[$segment];
            } elseif (is_object($target) && isset($target->{$segment})) {
                $target = $target->{$segment};
            } else {
                return value($default);
            }
        }

        return $target;
    }

    if (! function_exists('value')) {
        /**
         * Return the default value of the given value.
         *
         * @param  mixed  $value
         * @return mixed
         */
        function value($value, ...$args)
        {
            return $value instanceof Closure ? $value(...$args) : $value;
        }
    }

}