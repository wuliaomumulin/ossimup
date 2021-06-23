<?php
/***********************************************
 *		网络请求类扩展类
 * 文件: /library/spHttp.php
 * 说明: 网络请求类扩展类
 * 作者: Myxf
 * 更新: 2015年5月14日
 ***********************************************/

/**
 * 网络请求类扩展类
 */
class spHttp {

	/**
 	 * post方法
 	 * @access public
 	 * @param string $url 请求地址
 	 * @param string $data 提交数据
     * @param string $cookies 附加header头
     * @param string $cookies 附加cookie
 	 * @param string $file 是否上传文件 
     */
	function vpost($url,$data,$head='',$cookies = ""){
        $header = array(
            'Cookie: '.$cookies,
            $head,
        );
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
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
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

    /**
 	 * get方法
 	 * @access public
 	 * @param string $url 请求地址
 	 * @param string $cookies 附加cookie
 	 */
	function vget($url,$head='',$cookies = ""){
        $header = array(
            'Cookie: '.$cookies,
            $head,            
        );
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($curl, CURLOPT_COOKIEFILE, $cookies);
        $tmpInfo = curl_exec($curl);
        if (curl_errno($curl)) {
           return 'Error';
        }
        curl_close($curl);
        return $tmpInfo;
    }
    

    /**
     * $data方法
     * @access public
     * @param string $url 请求地址
     * @param string $cookies 附加cookie
     */
    function vdel($url,$data,$head='',$cookies = ""){
        $header = array(
            'Cookie: '.$cookies,
            $head,            
        );
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");   
        curl_setopt($curl, CURLOPT_POSTFIELDS,$data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_COOKIEFILE, $cookies);
        $tmpInfo = curl_exec($curl);
        if (curl_errno($curl)) {
           return 'Error';
        }
        curl_close($curl);
        return $tmpInfo;
    }


    /**
     * put方法
     * @access public
     * @param string $url 请求地址
     * @param string $cookies 附加cookie
     */
    function vput($url,$data,$head='',$cookies = ""){
        $header = array(
            'Cookie: '.$cookies,
            $head,            
        );
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");   
        curl_setopt($curl, CURLOPT_POSTFIELDS,$data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_COOKIEFILE, $cookies);
        $tmpInfo = curl_exec($curl);
        if (curl_errno($curl)) {
           return 'Error';
        }
        curl_close($curl);
        return $tmpInfo;
    }





    /**
     * 采集远程数据到本地
     * @access public
     * @param string $url 请求地址
     */
    function getDataSave($url,$path="../public/upload/") {
        $data = $this->vget($url); 
        $name = basename($url);
        if(!is_dir($path)){
            //如果目录不存在，创建目录
            spUploadFile::set_dir($path);
        }
        file_put_contents($path.$name,$data);
        $url = str_replace("../public", "", $path.$name);
        return $url;
    }

    /**
    *  判断网路是否通畅
    */
    function httpcode($url){
      $ch = curl_init();
      $timeout = 3;
      curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
      curl_setopt($ch, CURLOPT_HEADER, 1);
      curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
      curl_setopt($ch,CURLOPT_URL,$url);
      curl_exec($ch);
      return $httpcode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
      curl_close($ch);
    }

}

/* End of this file */