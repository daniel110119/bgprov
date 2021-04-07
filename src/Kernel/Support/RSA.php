<?php


namespace Bgprov\Kernel\Support;


class RSA
{
    public static function encrypt($data,$privateKey)
    {
        $publicKey = openssl_pkey_get_private($privateKey);
        if($publicKey){
            if (openssl_private_encrypt($data,$encryptData, $publicKey, OPENSSL_PKCS1_PADDING)) {
                openssl_free_key($publicKey);
                return base64_encode($encryptData);
            } else {
                throw new \RuntimeException(sprintf('：RSA加密失败; 当前数据(%s).', $data));
            }
        }else{
            throw new \RuntimeException(sprintf('：公匙有误; 当前数据(%s).', $data));
        }

    }

    public static function decrypt($encryptData,$privateKey)
    {
        $encryptData = base64_decode($encryptData);
        openssl_private_decrypt($encryptData, $decryptData, $privateKey);

        return $decryptData;
    }

    public static function getMode($key)
    {
        return 'aes-' . (8 * strlen($key)) . '-cbc';
    }

    public static function validateKey(string $key)
    {
        if (!in_array(strlen($key), [16, 24, 32], true)) {
            throw new \InvalidArgumentException(sprintf('：键长度必须为16、24或32个字节; 当前键长度 (%s).', strlen($key)));
        }
    }

    public static function validateIv(string $iv)
    {
        if (!empty($iv) && 16 !== strlen($iv)) {
            throw new \InvalidArgumentException('IV 向量长度不够16位.');
        }
    }

    public static function formatPem(string $publicKey)
    {
        $begin = "-----BEGIN PUBLIC KEY-----";
        $end = "-----END PUBLIC KEY-----";
        preg_match("/$begin(.*)$end/",$publicKey,$res);
        $pubPem = chunk_split($res[1], 64, "\n");
        return "$begin\n$pubPem$end\n";
    }
}