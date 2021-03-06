<?php


namespace Bgprov\Kernel;


use Bgprov\Kernel\Providers\ConfigServiceProvider;
use Bgprov\Kernel\Providers\HttpClientServiceProvider;
use Bgprov\Kernel\Providers\RequestServiceProvider;
use Bgprov\OpenPlatform\Application;
use Pimple\Container;

class ServiceContainer extends Container
{

    protected $providers;

    protected $defaultConfig;

    protected $userConfig;


    /**
     * ServiceContainer constructor.
     * @param $userConfig
     */
    public function __construct(array $config = [], array $prepends = [])
    {
        $this->userConfig = $config;
        parent::__construct($prepends);
        $this->registerProviders($this->getProviders());
    }


    public function getConfig()
    {
        $base = [
            'http' => [
                'timeout' => 30,
                'base_uri' => 'http://app.10085.shop/'
            ],
            'pem'=>[
                'path'=> dirname(__FILE__,6).'\\pem',
                'file_name'=>'private.pem'
            ],
            'user_id'=>1001,
        ];
        if (!empty($this->userConfig)){
            return array_replace_recursive($base, $this->userConfig);
        }else{
            return $base;
        }
    }

    public function getProviders()
    {
        return array_merge([
            ConfigServiceProvider::class,
            RequestServiceProvider::class,
            HttpClientServiceProvider::class,
        ], $this->providers);
    }

    public function registerProviders(array $providers)
    {
        foreach ($providers as $provider) {
            parent::register(new $provider());
        }
    }

}