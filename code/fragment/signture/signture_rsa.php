<?php

class Rsa2 
{
    private static $PRIVATE_KEY = 'private_key.pem';
    private static $PUBLIC_KEY  = 'public_key.pem';

    /**
     * 获取私钥
     * @return bool|resource
     */
    private static function getPrivateKey()
    {
        $privateKey = self::$PRIVATE_KEY;
        return openssl_pkey_get_private($privateKey);
    }

    /**
     * 获取公钥
     * @return bool|resource
     */
    private static function getPublicKey()
    {
        $publicKey = self::$PUBLIC_KEY;
        return openssl_pkey_get_public($publicKey);
    }

    /**
     * 私钥加密
     * @param string $data
     * @return null|string
     */
    public static function privateEncrypt($data = '')
    {
        if (!is_string($data)) {
            return null;
        }
        return openssl_private_encrypt($data,$encrypted,self::getPrivateKey()) ? base64_encode($encrypted) : null;
    }

    /**
     * 公钥加密
     * @param string $data
     * @return null|string
     */
    public static function publicEncrypt($data = '')
    {
        if (!is_string($data)) {
            return null;
        }
        return openssl_public_encrypt($data,$encrypted,self::getPublicKey()) ? base64_encode($encrypted) : null;
    }

    /**
     * 私钥解密
     * @param string $encrypted
     * @return null
     */
    public static function privateDecrypt($encrypted = '')
    {
        if (!is_string($encrypted)) {
            return null;
        }
        return (openssl_private_decrypt(base64_decode($encrypted), $decrypted, self::getPrivateKey())) ? $decrypted : null;
    }

    /**
     * 公钥解密
     * @param string $encrypted
     * @return null
     */
    public static function publicDecrypt($encrypted = '')
    {
        if (!is_string($encrypted)) {
            return null;
        }
        return (openssl_public_decrypt(base64_decode($encrypted), $decrypted, self::getPublicKey())) ? $decrypted : null;
    }

    /**
     * 创建签名
     * @param string $data 数据
     * @return null|string
     */
    public function createSign($data = '')
    {
        if (!is_string($data)) {
            return null;
        }
        return openssl_sign($data, $sign, self::getPrivateKey(), OPENSSL_ALGO_SHA256) ? base64_encode($sign) : null;
    }

    /**
     * 验证签名
     * @param string $data 数据
     * @param string $sign 签名
     * @return bool
     */
    public function verifySign($data = '', $sign = '')
    {
        if (!is_string($sign) || !is_string($sign)) {
            return false;
        }
        return (bool)openssl_verify($data, base64_decode($sign), self::getPublicKey(), OPENSSL_ALGO_SHA256);
    }
}

/*
创建公钥和私钥：
openssl genrsa -out private_key.pem 2048
openssl rsa -in private_key.pem -pubout -out public_key.pem
*/

$rsa2 = new Rsa2();
        
$privateEncrypt = $rsa2->privateEncrypt('锄禾日当午');
echo "私钥加密后:{$privateEncrypt}\n";

$publicDecrypt = $rsa2->publicDecrypt($privateEncrypt);
echo "公钥解密后:{$publicDecrypt}\n";

$publicEncrypt = $rsa2->publicEncrypt('锄禾日当午');
echo "公钥加密后:{$publicEncrypt}\n";

$privateDecrypt = $rsa2->privateDecrypt($publicEncrypt);
echo "私钥解密后:{$privateDecrypt}\n";

$sign = $rsa2->createSign('锄禾日当午');
echo "生成签名:{$privateEncrypt}\n";

$status = $rsa2->verifySign('锄禾日当午', $sign);
echo '验证签名:'.($status ? '成功' : '失败') ."\n";