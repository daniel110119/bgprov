<?php


namespace Bgprov\OpenPlatform\GetPack;


use Pimple\Container;

class ServiceProvider implements \Pimple\ServiceProviderInterface
{

    /**
     * @inheritDoc
     */
    public function register(Container $app)
    {
        $app['get_pack'] = function($app){
            return new Client($app);
        };
    }
}