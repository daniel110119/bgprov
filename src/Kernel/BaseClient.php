<?php


namespace Bgprov\Kernel;


use Bgprov\Kernel\Exceptions\RuntimeException;
use Bgprov\Kernel\ServiceContainer;
use Bgprov\Kernel\Support\Collection;
use Bgprov\Kernel\Support\File;
use Bgprov\Kernel\Support\RSA;

class BaseClient
{
    protected $app;

    protected $response;

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param mixed $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    protected $config;


    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param mixed $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * BaseClient constructor.
     * @param $app
     * @param $baseUri
     */
    public function __construct(ServiceContainer $app)
    {
        $this->app = $app;
        $this->setConfig($app['config']);
    }


    public function generatePrivateKey()
    {
        $config = $this->getConfig();
        $pem_config = $config->get('pem');
        if (!file_exists($pem_config['path'] . '\\' . $pem_config['file_name'])) {
            $response = $this->app['http_client']->request('POST', '/', [
                "query" => [
                    "a" => "GetRsa",
                    "c" => "app_oao_api"
                ],
                "form_params" => [
                    'userid' => $config->get('user_id')
                ]
            ]);
            $jsonString = $this->forMatResponse($response->getBody()->getContents());
            $response = json_decode($jsonString, true);
            $pem = RSA::formatPem($response['message']);
            File::write($pem_config['path'], $pem, $pem_config['file_name']);
        } else {
            $pem = file_get_contents($pem_config['path'] . '\\' . $pem_config['file_name']);
        }
        return $pem;
    }

    public function generateToken(): string
    {
        $user_id = $this->getConfig()->get('user_id');
        $token = RSA::encrypt("$user_id|" . time(), $this->generatePrivateKey());
        return $token;
    }


    public function toArray()
    {
        $jsonString = $this->forMatResponse($this->getResponse()->getBody()->getContents());
        return json_decode($jsonString, true);
    }

    public function forMatResponse(string $res)
    {
        return preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $res);
    }

    public function request(array $data)
    {
        $config = $this->getConfig();
        $response = $this->app['http_client']->request('POST', '/', [
            "query" => $this->query,
            "form_params" => $data
        ]);
        $this->setResponse($response);
        return $this;
    }

    public function all()
    {
        return $this->getResponse();
    }
}