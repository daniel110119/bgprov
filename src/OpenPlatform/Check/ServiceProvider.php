<?php


namespace Bgprov\OpenPlatform\Check;


use Pimple\Container;

class ServiceProvider implements \Pimple\ServiceProviderInterface
{

    /**
     * @inheritDoc
     */
    public function register(Container $app)
    {
        $app['get_check'] = function($app){
            return new Client($app);
        };
    }
}