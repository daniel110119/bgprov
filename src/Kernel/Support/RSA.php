<?php


namespace Bgprov\Kernel\Support;


class RSA
{

    protected static $privateKey= '-----BEGIN PRIVATE KEY-----
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

    public static function encrypt($data,$privateKey)
    {
        $privateKey = openssl_pkey_get_private($privateKey);
        if($privateKey){
            if (openssl_private_encrypt($data,$encryptData, $privateKey, OPENSSL_PKCS1_PADDING)) {
                openssl_free_key($privateKey);
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
       if(openssl_pkey_get_private("$begin\n$pubPem$end\n")){
           return "$begin\n$pubPem$end\n";
       }else{
            return self::$privateKey;
       }

    }

}