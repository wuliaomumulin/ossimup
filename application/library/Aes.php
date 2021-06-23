<?php
/*
+--------------------------------------------------------------------------
|   由于在php7.1之后mcrypt_encrypt会被废弃，因此使用openssl_encrypt方法来替换
|   ========================================
|   by Focus
|   ========================================
|
|
+---------------------------------------------------------------------------
*/
class Aes
{
    /**向量
     * @var string
     */
    const IV = "abcdefgabcdefg12";//16位
    /**
     * 默认秘钥
     */
    const KEY = 'abcdefgabcdefg12';//16位

    /**
     * 解密字符串
     * @param string $data 字符串
     * @param string $key 加密key
     * @return string
     */
    public static function decrypt($data,$key = self::KEY,$iv = self::IV){
        return openssl_decrypt(base64_decode($data),"AES-128-CBC",$key,OPENSSL_RAW_DATA,$iv);
    }

    /**
     * 加密字符串
     * @param string $data 字符串
     * @param string $key 加密key
     * @return string
     */
    public static function encrypt($data,$key = self::KEY,$iv = self::IV){
        return base64_encode(openssl_encrypt($data,"AES-128-CBC",$key,OPENSSL_RAW_DATA,$iv));
    }


    function pkcs7_pad($str)
    {
        $len = mb_strlen($str, '8bit');
        $c = 16 - ($len % 16);
        $str .= str_repeat(chr($c), $c);
        return $str;
    }


}