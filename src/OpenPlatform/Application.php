<?php


namespace Bgprov\OpenPlatform;


use Bgprov\Kernel\ServiceContainer;
use Bgprov\Kernel\Support\Str;


/**
 * Class Application
 * @property \Bgprov\OpenPlatform\Check\Client    $check
 * @property \Bgprov\OpenPlatform\GetMobil\Client $getMobil
 * @property \Bgprov\OpenPlatform\GetPack\Client  $getPack
 * @property \Bgprov\OpenPlatform\SubData\Client  $subData
 *
 */
class Application extends ServiceContainer
{
    /**
     * @var string[]
     */
    protected $providers = [
        GetMobil\ServiceProvider::class,
        Check\ServiceProvider::class,
        GetPack\ServiceProvider::class,
        SubData\ServiceProvider::class,
    ];

    public function hellp()
    {
        return [
            'method'=>[
                "check"=>[
                    '传入客户地址校验是否是诈骗'=>[
                        "address"=>'该地址中文需要URLCODE编码之后才能正确解析'
                    ]
                ],
                "get_mobil"=>[
                    '获取200个手机号码'=>[
                        "region"=>'区域代码同全省车牌首字母例如B代表绵阳的号码'
                    ]
                ],
                "获取套餐列表"=>[
                    '参数示例'=>[]
                ],
                "sub_data"=>[
                    '传入开卡信息'=>[
                        'mobile'=>'选择的手机号码',
                        'taocan'=>'套餐名称需要处理URLCODE转码',
                        'tccode'=>'套餐代码',
                        'truename'=>'真实姓名',
                        'idcard'=>'身份证号',
                        'tel'=>'第二联系方式',
                        'city'=>'所在城市需要URLCODE处理',
                        'area'=>'所在区域需要URLCODE处理',
                        'address'=>'不包含省市区收卡地址需要URLCODE处理',
                        'region'=>'号码所属区域字母',
                        'tcid'=>'套餐ID'
                    ]
                ],
            ]
        ];
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this[$name];
    }


}