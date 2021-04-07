基本使用（以服务端为例）:

```php

$defaultConfig =  [
            'http' => [
                'timeout' => 30,
                'base_uri' => 'http://app.10085.shop/'
            ],
            'pem'=>[
                'path'=>'xxxx\xxx\xxx',
                'file_name'=>'private.key'
            ],
            'user_id'=>1000,
        ];;

$app = Factory::make($config);
//可以只传['user_id'=>1001] 不传
$response = $app->sub_data->get(); 
 #check get_mobail get_pack
#返回collect 数组

```

