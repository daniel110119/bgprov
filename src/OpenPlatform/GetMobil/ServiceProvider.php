<?php


namespace Bgprov\OpenPlatform\GetMobil;


use Pimple\Container;

class ServiceProvider implements \Pimple\ServiceProviderInterface
{

    /**
     * @inheritDoc
     */
    public function register(Container $app)
    {
        $app['get_mobil'] = function($app){
            return new Client($app);
        };
    }
}