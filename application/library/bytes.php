<?php

/**

 * byte数组与字符串转化类

 */

class Bytes {

    private static function getPrivateKey()
    {
        $abs_path = dirname(__FILE__) . '/rsa_private_key.pem';
        $content = file_get_contents($abs_path);
        return $content;

    }

    /**
     * 获取公钥
     * @return bool|resource
     */
    private static function getPublicKey()
    {
        $abs_path = dirname(__FILE__) . '/rsa_public_key.pem';
        $content = file_get_contents($abs_path);
        return $content;

    }

//    public function rsa(){
//        echo '<hr />';
//
//        $content = "这是一段文字1111这是一段文字1111这是一段文字1111这是一段文字1111这是一段文字1111这是一段文字1111这是一段文字1111这是一段文字1111这是一段文字1111这是一段文字1111这是一段文字1111这是一段文字1111这是一段文字1111这是一段文字1111这是一段文字1111这是一段文字1111这是一段文字1111这是一段文字1111这是一段文字1111这是一段文字1111";
//        $res = $this->superLongPrivateKeyDecrypt($content,false,true);
//        echo '<hr />1';
//        var_dump($res);
//        //$content = "MVfHlZJcRk3dnSRJvQVmwqH20E+rR9FqjBGZBLD6tgdisG5/dhnbt19yLmNTvth/qZUe/bNjfifJL25eYuFa3Fy1GpBKFiVFe2cy5z7BhxsE6N+EJmwLHiQuipOjjQlx2RUrJ9CCkn1QqarOuju7tAzvF0vFyNlFblT5jKVCXrQ=";
//        $res = $this->superLongPublicKeyDecrypt($res,false,true);
//        echo '<hr />2';
//        var_dump($res);
////        echo '<hr />3';
////        print_r(self::getPublicKey());die;
//
//    }


    //公钥加密
    public function superLongPublicKeyEncrypt($content, $choicePath = true, $withBase64 = false)
    {
        if ($choicePath) {
            $pubKeyId = openssl_pkey_get_public(self::getPublicKey());//绝对路径读取
        } else {
            $pubKeyId = self::getPublicKey();//公钥
        }

        $RSA_ENCRYPT_BLOCK_SIZE = 117;

        $result = '';
        $data = str_split($content, $RSA_ENCRYPT_BLOCK_SIZE);
        foreach ($data as $block) {
            openssl_public_encrypt($block, $dataEncrypt, $pubKeyId, OPENSSL_PKCS1_PADDING);
            $result .= $dataEncrypt;
        }

        if ($withBase64) {
            return base64_encode($result);
        } else {
            return $result;
        }
    }


    //私钥解密
    public static function superLongPrivateKeyEncrypt($content, $choicePath = true, $withBase64 = false)
    {
        if ($choicePath) {
            $priKeyId = openssl_pkey_get_private(self::getPrivateKey());//绝对路径
        } else {
            $priKeyId = self::getPrivateKey();//私钥
        }

        if ($withBase64) {
            $data = base64_decode($content);
        }

        $RSA_DECRYPT_BLOCK_SIZE = 128;

        $result = '';
        $data = str_split($data, $RSA_DECRYPT_BLOCK_SIZE);
        foreach ($data as $block) {
            openssl_private_decrypt($block, $dataDecrypt, $priKeyId, OPENSSL_PKCS1_PADDING);
            $result .= $dataDecrypt;
        }

        if ($result) {
            return $result;
        } else {
            return false;
        }
    }

    //私钥加密

    public function superLongPrivateKeyDecrypt($content, $choicePath = true, $withBase64 = false)
    {
        if ($choicePath) {
            $pubKeyId = openssl_pkey_get_private(self::getPrivateKey());//绝对路径读取
        } else {
            $pubKeyId = self::getPrivateKey();//公钥
        }

        $RSA_ENCRYPT_BLOCK_SIZE = 117;

        $result = '';
        $data = str_split($content, $RSA_ENCRYPT_BLOCK_SIZE);

        foreach ($data as $block) {
            openssl_private_encrypt($block, $dataEncrypt, $pubKeyId, OPENSSL_PKCS1_PADDING);
            $result .= $dataEncrypt;
        }

        if ($withBase64) {
            return base64_encode($result);
        } else {
            return $result;
        }
    }

    //公钥解密

    public static function superLongPublicKeyDecrypt($content, $choicePath = true, $withBase64 = false)
    {
        if ($choicePath) {
            $priKeyId = openssl_pkey_get_public(self::getPublicKey());//绝对路径
        } else {
            $priKeyId = self::getPublicKey();//私钥
        }

        if ($withBase64) {
            $data = base64_decode($content);
        }

        $RSA_DECRYPT_BLOCK_SIZE = 128;

        $result = '';
        $data = str_split($data, $RSA_DECRYPT_BLOCK_SIZE);
        foreach ($data as $block) {
            openssl_public_decrypt($block, $dataDecrypt, $priKeyId, OPENSSL_PKCS1_PADDING);
            $result .= $dataDecrypt;
        }

        if ($result) {
            return $result;
        } else {
            return false;
        }
    }
}


//$bytes = new Bytes();
//$bytes->rsa();
?>