<?php


namespace Bgprov\Kernel\Support;


use Bgprov\Kernel\Exceptions\RuntimeException;

class File
{

    public static function createDir($path)
    {
        if (file_exists($path)) {
            return true;
        } else {
            if (mkdir($path, 0700, true)) {
                return true;
            } else {
                throw new RuntimeException(sprintf(':文件夹创建失败   当前文件名 (%s).', $path));
            }
        }
    }

    public static function write(string $path, string $data, string $name)
    {
        $$path = iconv('utf-8', 'gb2312', $path);
        $fileName = "$path$name";
        if (self::createDir($path)) {
            $fp = fopen($fileName, 'w');
            fwrite($fp, $data);
            fclose($fp);
        } else {
            throw new RuntimeException(sprintf(':文件创建失败   当前文件 (%s).', $fileName));
        }
    }

}