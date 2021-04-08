<?php


namespace Bgprov\OpenPlatform\GetPack;


use Bgprov\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * @var string[]
     */
    public $query = [
        'a'=>'GetPack',
        'c'=>'app_oao_api'
    ];

    /**
     * @param array $data
     * @return \Bgprov\Kernel\Support\Collection
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