<?php


namespace Bgprov\Kernel\Providers;


use Bgprov\Kernel\Config;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class ConfigServiceProvider implements ServiceProviderInterface
{

    public function register(Container $pimple)
    {
        !isset($pimple['config']) && $pimple['config'] = function ($app) {
            return new Config($app->getConfig());
        };
    }
}