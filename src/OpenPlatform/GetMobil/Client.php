<?php


namespace Bgprov\OpenPlatform\GetMobil;




use Bgprov\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * @var string[]
     */
    public $query = [
        'a'=>'GetMobile',
        'c'=>'app_oao_api'
    ];

    /**
     * @param array $data
     * @return \Bgprov\Kernel\Support\Collection region=区域代码同全省车牌首字母例如A代表郑州的号码
     */
    public function get(array $data)
    {
        $data['userid'] = $this->getConfig()->get('user_id');
        $data['token'] = $this->generateToken();
        $response = $this->request($data);
        return collect($response->toArray());
    }

    public function raw (array $data)
    {
        $data['userid'] = $this->getConfig()->get('user_id');
        $data['token'] = $this->generateToken();
        $response = $this->request($data);
        return $response->all();
    }
}