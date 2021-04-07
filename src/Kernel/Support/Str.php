<?php


namespace Bgprov\Kernel\Support;


use Bgprov\Kernel\Exceptions\RuntimeException;

class Str
{
    protected static $studlyCache = [];


    public static function random($lens = 16)
    {
        $str = '';
        while (($len = strlen($str)) < $lens) {
            $size = $lens - $len;

            $bytes = static::randomBytes($size);

            $str .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $str;
    }

    public static function randomBytes($lens = 16)
    {
        if (function_exists('random_bytes')) {
            $bytes = random_bytes($lens);
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            $bytes = openssl_random_pseudo_bytes($lens, $strong);
            if (false === $bytes || false === $strong) {
                throw new RuntimeException('无法生成随机字符串');
            }
        } else {
            throw new RuntimeException('没有打开OpenSSL扩展');
        }

        return $bytes;
    }

    public static function studly($value)
    {
        $key = $value;

        if (isset(static::$studlyCache[$key])) {
            return static::$studlyCache[$key];
        }

        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return static::$studlyCache[$key] = str_replace(' ', '', $value);
    }
}