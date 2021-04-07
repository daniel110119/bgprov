<?php


namespace Bgprov\OpenPlatform;


use Bgprov\Kernel\ServiceContainer;
use Bgprov\Kernel\Support\Str;


/**
 * Class Application
 * @property \Bgprov\OpenPlatform\Check\Client    $check
 * @property \Bgprov\OpenPlatform\GetMobil\Client $getMobil
 * @property \Bgprov\OpenPlatform\GetPack\Client  $getPack
 * @property \Bgprov\OpenPlatform\SubData\Client  $subData
 *
 */
class Application extends ServiceContainer
{
    /**
     * @var string[]
     */
    protected $providers = [
        GetMobil\ServiceProvider::class,
        Check\ServiceProvider::class,
        GetPack\ServiceProvider::class,
        SubData\ServiceProvider::class,
    ];

    /**
     * @param $method
     * @param $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return $this['get_mobil']->$method(...$arguments);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this[$name];
    }
}