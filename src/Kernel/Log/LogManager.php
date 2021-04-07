<?php


namespace Bgprov\Kernel\Log;


use Bgprov\Kernel\ServiceContainer;
use Psr\Log\LoggerInterface;


class LogManager
{

    protected $app;
    protected $channels = [];
    protected $customCreators = [];
    private $logger;

    public function __construct(ServiceContainer $app)
    {
        $this->app = $app;

    }


}

