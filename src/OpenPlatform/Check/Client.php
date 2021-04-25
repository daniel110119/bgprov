<?php


namespace Bgprov\OpenPlatform\Check;


use Bgprov\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * @var string[]
     */
    public $query = [
        'a'=>'Check',
        'c'=>'app_oao_api'
    ];

    /**
     * @param array $data  address=该地址中文需要URLCODE编码之后才能正确解析
     * @return \Bgprov\Kernel\Support\Collection
     */
    public function get(array $data=[])
    {
        $data['userid'] = $this->getConfig()->get('user_id');
        $data['token'] = $this->generateToken();
        $response = $this->request($data);
        return collect($response->toArray());
    }

    public function raw (array $data=[])
    {
        $data['userid'] = $this->getConfig()->get('user_id');
        $data['token'] = $this->generateToken();
        $response = $this->request($data);
        $response = $response->all();
        $original = $response->getBody()->getContents();
        $res = [
            'original'=>$original,
            'data'=>json_decode($original,true)
        ];
        return $res;
    }





}