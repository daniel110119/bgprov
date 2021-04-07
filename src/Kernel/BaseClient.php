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

    protected $baseUri;

    protected $requestData;

    protected $config;

    protected $privateKeyFilePath = '-----BEGIN PRIVATE KEY-----
MIIEvwIBADANBgkqhkiG9w0BAQEFAASCBKkwggSlAgEAAoIBAQDV+WQCxvFnUtiq
rwx5B3v/jBl4VEOI4v3fsa+6M/tSnzciW4eFbVhPxpTh9HbM6Hgihj02AAQRxRLu
oFu8B4o+5MpKySTJUeYYKUBEHoq6afBKfhSJsM7xxfim0kabrUxqkAOqsmQtP4BE
8/qlw4CDv/DD9XxDM/1sjZlIi1E4WqC8NTOVCZMpL3j1nI4ZTd9Sl8cUMGP8CsJm
Fc/bo4Iu3RKLhXtyDGqcIVQYxTVIZZpIiYHEVC0sXUvdJVF/PZ1jSWcrP4I6zft5
XoXy8BljHxSRTMlhxaknsNaHXiH3CGmA4gg98tZ75+RyRuML6ACAek9u2g2a9Ad+
cuzOdB5XAgMBAAECggEAajVZ8Q7ysEitBzvQQxr174iI8/NPeljBjxFRmmlG7GHf
KgyonhACb8awC+AeP+N8Dhb3tyAW5QMfPJcYMaLEeidySeDBg7RFM+T141oNni4L
ec5PP3Elp1iO7a8eIRPKBHLrfwUBOHx2nMNFvJXn6n2RcpMdVPYvanx2g02zyPY6
wJDeZ0IEeO+Y8trEuaHMhD73WZ9K/JOqb/1k/7gXHgY+rAX6T3FsFEEW3CbG4yjM
yL+/nDS6KUc0Et8U+MZGEuPkWc7X5W33GYoQyTa8mGABMxOIhTvmi/Q2QkSs6Bcy
AIxtO3k+qi2GdCYRjyHonpp0qLkVLokTJkEyFkp3EQKBgQDvDtgGag8WQsObC93a
PcBzmP50I14A0dymkUrHUkYJkOlLstslUQhPXmLQlka9IcTIwb1tuZo96SgedxTr
8vgkAze6MHQdUDdlyN1QoULOciA4lW2X88OF2z7Zd/vpXvEovGQC+ZPvvyQcjcro
9JV9posVcWknuFbzlTpcsP1JtQKBgQDlI3WEfRZRLzsbe1HyezEz9dJVwN/zKjDS
2cimICeq5zlKtU9ML6/rqe1UVElHK67XtzUGdY/oYmh2DQB8RtHYrvRsGsR0mzdH
a/dwgSqMOCX1iTiMYZYzCgDuazMSk980XfJ+RZAqDxh8IEITA0JFJi7ZxCrr9q3f
xZ2rbUYfWwKBgQDNKPwUZSkVfa7JfWGkTqK/kmmt2jxuj6zB9qcqcF1TEF5nbNPO
LsDm/KXpRU7oOfbcJAkiBcttno+jtabq59Y1cd19mN/N2G3ymHP3Iq5m5mLorLUl
VzpRwlsp32L+YVCuGR/rva1Mb8naB4ZAbsds0mVCvdX1FKrq2QMBUZPOkQKBgQCJ
62D14TDIpuwl64F0uW/xPZpf4VsrCw8eKtYaICqgNNMIQ7+6NM3nxH1EkAMXYpS3
5N92xtZa4rjLraHIK/xtN0mJtEbhhPet9q+WOTj582Xtt62g2bBFglTzLPUtznHA
HHg0RvyyRCnRtLzworqF0qjKjMflBbK8iT8RCxH1FwKBgQCaMS6kWQJXAse/vjIA
dMqSNCawHflt+QUTlsHDCrpD0+ZUw4KwMqri5WWq09DXdIIx5p5bkM+8QADp3msK
Sz3884WH9H4jqZYGRKmGWW7y2E6XVgzwvA+4XMsFJKOFPBtHDXRVRPe6WsM0oQvk
7i3yQKV/aMgFm72TSoTMWngTsA==
-----END PRIVATE KEY-----
';

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


    private function generatePrivateKey(bool $force = false)
    {
        $config = $this->getConfig();
        $pem_config =$config->get('pem');
        if ($force) {
            $response = $this->app['http_client']->request('POST','/',[
                "query"=>[
                    "a"=>"GetRsa",
                    "c"=>"app_oao_api"
                ],
                "form_params"=>[
                    'userid'=>$config->get('user_id')
                ]
            ]);
            $response = $this->toArray($response->getBody()->getContents());
            $pem = RSA::formatPem($response['message']);
            $file = File::write($pem_config['path'],$this->privateKeyFilePath,$pem_config['file_name']);
        } else {
            $pem = file_get_contents($pem_config['path'].$pem_config['file_name']);
        }
        return $pem;
    }

    public function generateToken():string
    {
        $user_id = $this->getConfig()->get('user_id');
        $token = RSA::encrypt("$user_id|".time(),$this->generatePrivateKey());
        return $token;
    }
    /**
     * @return mixed
     */
    public function getRequestData()
    {
        return $this->requestData;
    }

    /**
     * @param mixed $requestData
     */
    public function setData($requestData)
    {
        $this->requestData = $requestData;
    }


    public function toArray(string $jsonString)
    {
        $jsonString = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $jsonString);
        return json_decode($jsonString,true);

    }

    public function request(array $data)
    {
        $config = $this->getConfig();
        $response = $this->app['http_client']->request('POST','/',[
            "query"=>$this->query,
            "form_params"=>$data
        ]);
        $response = $this->toArray($response->getBody()->getContents());
        return $response;
    }
}