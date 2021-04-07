<?php


namespace Bgprov;


use Bgprov\Kernel\Support\Str;
use Symfony\Bridge\PsrHttpMessage\Tests\Fixtures\App\Kernel;

/**
 * Class Factory
 *
 * @package Bgprov
 *
 *  @method static \Bgprov\OpenPlatform\Application  openPlatform(array $config)
 *
 */
class Factory
{

    /**
     * @param $name
     * @param array $config
     * @return mixed
     */
    public static function make( array $config,string $name='open-platform')
    {
        $namespace = Str::studly($name);;
        $application = "\\Bgprov\\{$namespace}\\Application";
        return new $application($config);
    }


    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return self::make($name, ...$arguments);
    }



}