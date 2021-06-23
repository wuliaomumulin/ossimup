<?php
/***********************************************
 *		Curl请求类
 ***********************************************/


class Curl {

	/**
 	 * post方法
 	 * @access public
 	 * @param string $url 请求地址
 	 * @param string $data 提交数据
     * @param array $header 附加header头,是一个值对的数组
     * @param string $cookies 附加cookie 
     */
	public function post($url,$data,$header='',$cookies = ""){
        $curl = curl_init();

        if (class_exists('\CURLFile')) {        // 这里用特性检测判断php版本
            curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
            if(is_array($data)&&!empty($data)){
                foreach ($data as $k=>&$v) {
                    $v = curl_file_create($v);
                }   
            } 
        }else{
           if (defined('CURLOPT_SAFE_UPLOAD')) {
            curl_setopt($curl, CURLOPT_SAFE_UPLOAD, false);
            }    
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);//将CA证书设置为不用验证
        //curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	     curl_setopt($curl, CURLOPT_COOKIEFILE, $cookies);
        $tmpInfo = curl_exec($curl);
        if (curl_errno($curl)) {
           return 'Error'.curl_errno($curl);
        }
        curl_close($curl);
        return $tmpInfo;
    }

}

/* End of this file */