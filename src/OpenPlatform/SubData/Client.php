<?php


namespace Bgprov\OpenPlatform\SubData;


use Bgprov\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * @var string[]
     */
    public $query = [
        'a'=>'SubData',
        'c'=>'app_oao_api'
    ];

    /**
     * @param array $data
     * mobile=选择的手机号码
     * taocan=套餐名称需要处理URLCODE转码
     * tccode=获取的套餐代码
     * truename=真实姓名
     * idcard=身份证号
     * tel=第二联系方式
     * city=所在城市需要URLCODE处理
     * area=所在区域需要URLCODE处理
     * address=不包含省市区收卡地址需要URLCODE处理
     * region=号码所属区域字母
     * tcid=获取的套餐ID
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