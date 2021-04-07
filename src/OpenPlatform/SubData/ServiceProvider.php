<?php


namespace Bgprov\OpenPlatform\SubData;


use Pimple\Container;

class ServiceProvider implements \Pimple\ServiceProviderInterface
{

    /**
     * @inheritDoc
     */
    public function register(Container $app)
    {
        $app['sub_data'] = function($app){
            return new Client($app);
        };
    }
}